<?php
include_once "Tournoi.php";
include_once "Database.php";
include_once "Jeu.php";
//créer la liste de tournoi
class Tournois
{
    private $tournois;
    private $posMap;
    //constructeur
    public function __construct(){
        $this->tournois = array();
    }
    //selectionner un tournoi mysql
    private function selectTournoi(string $cond=""): void
    {
        $mysql = Database::getInstance();
        $data = $mysql->select("T.IdTournoi, T.NomTournoi, T.CashPrize, T.Notoriete, T.Lieu, T.DateHeureTournois,
          J.IdJeu, J.NomJeu, J.TypeJeu, J.TempsDeJeu, J.DateLimiteInscription", "Tournois T, Contenir C, Jeu J", "where C.IdJeu = J.IdJeu
            AND C.IdTournoi = T.IdTournoi ".$cond.' ORDER BY IdTournoi');
        $this->misAJourListeTournois($data);
    }
    //mettre a jour la liste des tournois
    private function misAJourListeTournois($data){
        $this->tournois = array();
        $this->posMap = array();
        $last = -1;
        $index = -1;
        foreach ($data as $ligne) {
            if($last != $ligne['IdTournoi']){ 
                $this->tournois[] = new Tournoi($ligne['IdTournoi'],$ligne['NomTournoi'], $ligne['CashPrize'],
                $ligne['Notoriete'], $ligne['Lieu'], $ligne['DateHeureTournois'], 
                array($ligne['IdJeu'], new Jeu($ligne['IdJeu'],$ligne['NomJeu'], $ligne['TypeJeu'], $ligne['TempsDeJeu'], $ligne['DateLimiteInscription'])));
                $last = $ligne['IdTournoi'];
                $index+=1;
            }else{
                $this->tournois[$this->posMap[$ligne['IdTournoi']]]->ajouterJeu($ligne['IdJeu'],new Jeu($ligne['IdJeu'],$ligne['NomJeu'], $ligne['TypeJeu'], $ligne['TempsDeJeu'], $ligne['DateLimiteInscription']));
            }
            $this->posMap[$ligne['IdTournoi']] =  $index;
        }
    }
    //récupérer tous les tournois
    public function tousLesTournois()
    {
        $this->selectTournoi();
    }
    //récupérer les tournois par équipes
    public function TournoisEquipe($cond)
    {
        $mysql = Database::getInstance();
        $data = $mysql->select("T.IdTournoi, T.NomTournoi, T.CashPrize, T.Notoriete, T.Lieu, T.DateHeureTournois,
        J.IdJeu, J.NomJeu, J.TypeJeu, J.TempsDeJeu, J.DateLimiteInscription","Tournois T, Contenir C, Jeu J , Equipe E, Participer P","where C.IdJeu = J.IdJeu
          AND C.IdTournoi = T.IdTournoi AND T.IdTournoi=P.IdTournoi AND P.IdEquipe=E.IdEquipe AND E.IdJeu=J.IdJeu AND E.IdEquipe=".$cond.' ORDER BY  IdTournoi');
        $this->misAJourListeTournois($data);
    }
    //récupérer les tournois par équipes pas joué
    public function TournoisEquipeNJ($cond,$id)
    {
        $mysql = Database::getInstance();
        $data = $mysql->select("T.IdTournoi, T.NomTournoi, T.CashPrize, T.Notoriete, T.Lieu, T.DateHeureTournois,
        J.IdJeu, J.NomJeu, J.TypeJeu, J.TempsDeJeu, J.DateLimiteInscription","Tournois T, Contenir C, Jeu J"," where C.IdJeu = J.IdJeu
        AND C.IdTournoi = T.IdTournoi AND  J.IdJeu='".$cond."' 
        AND T.IdTournoi not in (select DISTINCT T.IdTournoi from Tournois T, Contenir C, Jeu J , Equipe E, Participer P where C.IdJeu = J.IdJeu
        AND C.IdTournoi = T.IdTournoi AND T.IdTournoi=P.IdTournoi AND P.IdEquipe=E.IdEquipe AND E.IdJeu=J.IdJeu AND E.IdEquipe='".$id."')");
        $this->misAJourListeTournois($data);
    }
    //selection de tournois (filtre)
    public function tournoiDe(string $nomJeu="", string $nomTournois="", float $prixMin=0,float $prixMax=0,string $notoriete="",string $lieu="",string $date=""): void
    {
        if($nomJeu=="" && $nomTournois==="" && $prixMin===0 && $prixMax===0 && $notoriete==="" && $lieu==="" && $date===""){
            throw new \RuntimeException("Accun Argument passé");
        }
        $cond = "AND";
        if($nomJeu !== ""){
            $cond.= " Lower(J.NomJeu) like Lower('%$nomJeu%') AND";
        }
        if($nomTournois !== ""){
            $cond.=" Lower(T.NomTournoi) like Lower('%$nomTournois%') AND";
        }
        if($prixMax != 0){
            $cond.=" T.CashPrize <= $prixMax AND";
        }
        if($prixMin != 0){
            $cond.=" T.CashPrize >= $prixMin AND";
        }
        if($notoriete !== ""){
            $cond.=" T.Notoriete = '$notoriete' AND";
        }
        if($lieu !== ""){
            $cond.=" Lower(T.Lieu) like Lower('%$lieu%') AND";
        }
        if($date !== ""){
            $cond.=" Date(T.DateHeureTournois) = '$date' AND";
        }
        $cond = substr($cond, 0, -3);
        $this->selectTournoi($cond);
    }
    //afficher les infos des tournois
    public function afficherTournois()
    {
        foreach ($this->tournois as $ligneValue) {
        echo "<tr>";
            $tournoi = $ligneValue->listeInfo();
            foreach ($tournoi as $colValue) {
                echo "<td>", $colValue, "</td>";
            }
            echo "<td><a href='../page/DetailsTournoi.php?IDT=". $ligneValue->getIdTournoi()."'><img class='imgB' src='../img/Detail.png' alt='Details'></a></td>";
        echo "</tr>";
        }
    }
    //récupérer les tournois
    public function getTournois(){
        return $this->tournois;
    }
    //récupérer un tournoi par son id
    public function getTournoi($id){
        return $this->tournois[$this->posMap[$id]];
    }
}
?>