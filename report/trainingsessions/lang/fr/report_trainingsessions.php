<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'report_trainingsessions'.
 *
 * @package    report
 * @subpackage trainingsessions
 * @author     Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['activitytime'] = 'Temps en activit�s : ';
$string['advancement'] = 'Avancement';
$string['allgroups'] = 'Tous les groupes';
$string['ashtml'] = 'Format HTML';
$string['asxls'] = 'T�l�charger au format Excel';
$string['chooseagroup'] = 'Choisir un groupe';
$string['chooseaninstitution'] = 'Choisir une institution';
$string['connections'] = 'Connexions';
$string['course'] = 'Formation';
$string['coursename'] = 'Nom du groupe';
$string['courseglobals'] = 'Espaces globaux du cours';
$string['done'] = 'R�alis� : ';
$string['elapsed'] = 'Temps total';
$string['enddate'] = 'Date de fin';
$string['equlearningtime'] = 'Temps �quivalent de formation : ';
$string['evaluating'] = 'Evaluation';
$string['executing'] = 'Formation';
$string['firstname'] = 'Pr�nom';
$string['freerun'] = 'Parcours libre';
$string['generateXLS'] = 'G�n�rer en XLS';
$string['generatereports'] = 'G�n�rer les rapports';
$string['headsection'] = 'Section d\'en-t�te';
$string['hits'] = 'Hits';
$string['institution'] = 'Etablissement';
$string['institutions'] = 'Etablissements';
$string['instructure'] = 'Temps dans la structure : ';
$string['item'] = 'El�ment';
$string['lastname'] = 'Nom';
$string['nostructure'] = 'Pas de structure mesurable identifi�e';
$string['over'] = 'sur';
$string['outofstructure'] = 'Temps hors structure : ';
$string['parts'] = 'Parties';
$string['pluginname'] = 'Sessions de formation';
$string['nosessions'] = 'Aucune session enregistr�e';
$string['sessions'] = 'Sessions';
$string['reports'] = 'Rapports de formation';
$string['role'] = 'R�le';
$string['sectionname'] = 'Nom de la s�quence';
$string['seedetails'] = 'Voir la fiche de d�tail';
$string['selectforreport'] = '';
$string['selectforreport'] = 'Inclure dans les rapports';
$string['sessionreports'] = 'Rapports de formation individuel';
$string['startdate'] = 'Date de d�but';
$string['timeperpart'] = 'Temps par partie';
$string['totalduration'] = 'Dur�e totale par s�quence';
$string['trainingreports'] = 'Rapports de Formation';
$string['trainingsessions'] = 'Rapports de session';
$string['trainingsessions:view'] = 'Voir les rapports de session de formation';
$string['trainingsessions_report_advancement'] = 'Rapport d\'avancement';
$string['trainingsessions_report_connections'] = 'Rapport des connexions';
$string['trainingsessions_report_connections'] = 'Rapport sur les connexions';
$string['trainingsessions_report_institutions'] = 'Rapport des �tablissements';
$string['trainingsessionsreport'] = 'Rapports de session de formation';
$string['unvisited'] = 'Non visit�';
$string['updatefromcoursestart'] = 'A partir du d�but de la formation';
$string['user'] = 'Par participant';
$string['workingsessions'] = 'Sessions de travail : ';

$string['activitytime_help'] = 'Ce temps comptabilise les temps pass�s dans les "activit�s" du cours, � l\'exception des temps
	d\'usage pass�s dans les espaces communs du cours. Il peut prendre en compte dans certains cas
	(utilisation du module Checklist "am�lior�" avec prise en charge des temps forfaitaires -- 
	http://github.com/vfremaux/moodle-mod_checklist.git), les
	temps forfaitis�s associ�s aux activit�s plutot que les temps mesur�s sur les traces.';

$string['equlearningtime_help'] = '
<p>Le temps �quivalent de formation tient compte de tous les temps pass�s dans le cours
	y compris les temps forfaitaires fournis par le module Checklist modifi� 
	(http://github.com/vfremaux/moodle-mod_checklist.git).</p>
';

$string['checklistadvice_help'] = '
<p>Si une "liste de suivi" (module non standard) est utilis�e dans le cours pour marquer les
	accomplissments, certaines ativit�s peuvent �tre valid�es par les enseignants sans aucune
	interaction pr�alable de l\'�tudiant dans le cours (et donc sans traces).</p>
<p>Ceci est une situation possible et normale et le rapport affiche des informations "justes"
	dans ses diff�rents indicateurs d\'usage.</p>
';

?>