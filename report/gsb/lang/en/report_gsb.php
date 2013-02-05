<?php
// This file is part of GSB module for Moodle - http://moodle.org/
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
 * Version info for GSB Report
 *
 * @package    report
 * @subpackage GSB
 * @copyright  2012 onwards Richard Havinga richard.havinga@southampton-city.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$string['pluginname'] = 'GSB medals';
$string['gsb:viewmygsbreport'] = 'View the Gold, Silver, Bronze course medal';
$string['gsbadmin'] = 'Gold, Silver, Bronze Admin Page';
$string['gsbdepartment'] = 'Gold, Silver, Bronze Moderation Page';
$string['title'] = 'title';
$string['subcategories'] = 'Include Sub Categories';
$string['subcategoriesxp'] = 'Do you want to include sub categories when awarding medals?';
$string['automedal'] = 'Automatic Awards';
$string['automedalxp'] = 'Do you want to automatically award auto calculated medals when a teacher or administrator enters a course';
$string['studentviews'] = 'Average Student Views';
$string['studentviewsxp'] = 'The minimum average student views before being able to have a calculated medal score. All courses without this criteria will be marked as in development';
$string['minenrolments'] = 'Minimum Number of enrolments';
$string['minenrolmentsxp'] = 'The minimum number of enrolments needed on a course before being able to have a calculated medal score. All courses without this criteria will be marked as in development';

//bronze settings --------------------------------------------------------------

$string['bronzenumoptional'] = "Optional Requirements";
$string['bronzenumoptionalxp'] = "This is the number of optional settings needed to be fulfilled before awarding this 
medal. Use this setting when benchmarking to a selection of a range of activities. Note: select only the minimum number of criteria.";
$string['bronze_heading'] = 'Configure Bronze Benchmarking';
$string['explaingeneralbronze'] = 'These are the settings used for the Bronze medal auto calculations.';

$string['configbronzeresources'] = 'Resources Criteria Type';
$string['configbronzeresourcesxp'] = 'Define whether the minimum resources count should be excluded, mandatory or optional to this medal criteria.';
$string['bronzeresources']= 'Number of Resources';
$string['configdefaultbronzeresources'] = 'Number of resources required for Bronze if used. This is a total of documents within books,
labels, pages and on the main course page.';

$string['configbronzeassignments'] = 'Assignments Criteria Type';
$string['configbronzeassignmentsxp'] = 'Define whether the minimum number of assignments count should be excluded, mandatory or optional to this medal criteria.';
$string['bronzeassignments'] = 'Number of Assignments';
$string['configdefaultbronzeassignments'] = 'Number of assignments required for Bronze if used. This is the total of assignments within a course page.';

$string['configbronzefeedback'] = 'Feedback Criteria Type';
$string['configbronzefeedbackxp'] = 'Define whether the minimum number of Feedback count should be excluded, mandatory or optional to this medal criteria.';
$string['bronzefeedback'] = 'Number of Feedback Activities';
$string['configdefaultbronzefeedback'] = 'Number of Feedback activities required for Bronze if used.';

$string['configbronzeims'] = 'IMS package Criteria Type';
$string['configbronzeimsxp'] = 'Define whether the minimum number of IMS package count should be excluded, mandatory or optional to this medal criteria.';
$string['bronzeims'] = 'Number of IMS packages';
$string['configdefaultbronzeims'] = 'Number of IMS packages required for Bronze if used.';

$string['configbronzequest'] = 'Questionnaire Criteria Type';
$string['configbronzequestxp'] = 'Define whether the minimum number of questionnaire count should be excluded, mandatory or optional to this medal criteria.';
$string['bronzequest'] = 'Number of Questionnaires';
$string['configdefaultbronzequest'] = 'Number of Questionnaires required for Bronze if used.';

$string['configbronzequiz'] = 'Quiz Criteria Type';
$string['configbronzequizxp'] = 'Define whether the minimum number of Quiz count should be excluded, mandatory or optional to this medal criteria.';
$string['bronzequiz'] = 'Number of Quizzes';
$string['configdefaultbronzequiz'] = 'Number of Quizzes required for Bronze if used.';

$string['configbronzeembed'] = 'Embed Criteria Type';
$string['configbronzeembedxp'] = 'Define whether the minimum number of embedded videos count should be excluded, mandatory or optional to this medal criteria.';
$string['bronzeembed'] = 'Number of embedded videos';
$string['configdefaultbronzeembed'] = 'Number of embedded videos required for Bronze if used.';

$string['configbronzechat'] = 'Chat Criteria Type';
$string['configbronzechatxp'] = 'Define whether the minimum number of chat activity count should be excluded, mandatory or optional to this medal criteria.';
$string['bronzechat'] = 'Number of Chat Activities';
$string['configdefaultbronzechat'] = 'Number of chat activities required for Bronze if used.';

$string['configbronzeforum'] = 'Forum Criteria Type';
$string['configbronzeforumxp'] = 'Define whether the minimum number of forum count should be excluded, mandatory or optional to this medal criteria.';
$string['bronzeforum'] = 'Number of Forums';
$string['configdefaultbronzeforum'] = 'Number of Forums required for Bronze if used.';

$string['configbronzewiki'] = 'Wiki Criteria Type';
$string['configbronzewikixp'] = 'Define whether the minimum number of Wiki\'s count should be excluded, mandatory or optional to this medal criteria.';
$string['bronzewiki'] = 'Number of Wiki Activities';
$string['configdefaultbronzewiki'] = 'Number of Wiki Activities required for Bronze if used.';

$string['configbronzebook'] = 'Book Criteria Type';
$string['configbronzebookxp'] = 'Define whether the minimum number of Books should be excluded, mandatory or optional to this medal criteria.';
$string['bronzebook'] = 'Number of Books';
$string['configdefaultbronzebook'] = 'Number of Books required for Bronze if used.';

$string['configbronzedatabase'] = 'Database Criteria Type';
$string['configbronzedatabasexp'] = 'Define whether the minimum number of databases should be excluded, mandatory or optional to this medal criteria.';
$string['bronzedatabase'] = 'Number of Databases';
$string['configdefaultbronzedatabase'] = 'Number of Databases required for Bronze if used.';

$string['configbronzeworkshop'] = 'Workshop Criteria Type';
$string['configbronzeworkshopxp'] = 'Define whether the minimum number of workshops should be excluded, mandatory or optional to this medal criteria.';
$string['bronzeworkshop'] = 'Number of Workshops';
$string['configdefaultbronzeworkshop'] = 'Number of Workshops required for Bronze if used.';

$string['configbronzechoice'] = 'Choice Criteria Type';
$string['configbronzechoicexp'] = 'Define whether the minimum number of Choice Activities should be excluded, mandatory or optional to this medal criteria.';
$string['bronzechoice'] = 'Number of Choice Activities';
$string['configdefaultbronzechoice'] = 'Number of Choice activities required for Bronze if used.';

$string['configbronzeglossary'] = 'Glossary Criteria Type';
$string['configbronzeglossaryxp'] = 'Define whether the minimum number of glossaries should be excluded, mandatory or optional to this medal criteria.';
$string['bronzeglossary'] = 'Number of Glossaries';
$string['configdefaultbronzeglossary'] = 'Number of Glossaries required for Bronze if used.';

//silver settings --------------------------------------------------------------

$string['silvernumoptional'] = "Optional Requirements";
$string['silvernumoptionalxp'] = "This is the number of optional settings needed to be fulfilled before awarding this 
medal. Use this setting when benchmarking to a selection of a range of activities. Note: select only the minimum number of criteria.";
$string['silver_heading'] = 'Configure Silver Benchmarking';
$string['explaingeneralsilver'] = 'These are the settings used for the silver medal auto calculations. Note: a course must meet bronze criteria before being assessed for silver criteria. ';

$string['configsilverresources'] = 'Resources Criteria Type';
$string['configsilverresourcesxp'] = 'Define whether the minimum resources count should be excluded, mandatory or optional to this medal criteria.';
$string['silverresources']= 'Number of Resources';
$string['configdefaultsilverresources'] = 'Number of resources required for silver if used. This is a total of documents within books,
labels, pages and on the main course page.';

$string['configsilverassignments'] = 'Assignments Criteria Type';
$string['configsilverassignmentsxp'] = 'Define whether the minimum number of assignments count should be excluded, mandatory or optional to this medal criteria.';
$string['silverassignments'] = 'Number of Assignments';
$string['configdefaultsilverassignments'] = 'Number of assignments required for silver if used. This is the total of assignments within a course page.';

$string['configsilverfeedback'] = 'Feedback Criteria Type';
$string['configsilverfeedbackxp'] = 'Define whether the minimum number of Feedback count should be excluded, mandatory or optional to this medal criteria.';
$string['silverfeedback'] = 'Number of Feedback Activities';
$string['configdefaultsilverfeedback'] = 'Number of Feedback activities required for silver if used.';

$string['configsilverims'] = 'IMS package Criteria Type';
$string['configsilverimsxp'] = 'Define whether the minimum number of IMS package count should be excluded, mandatory or optional to this medal criteria.';
$string['silverims'] = 'Number of IMS packages';
$string['configdefaultsilverims'] = 'Number of IMS packages required for silver if used.';

$string['configsilverquest'] = 'Questionnaire Criteria Type';
$string['configsilverquestxp'] = 'Define whether the minimum number of questionnaire count should be excluded, mandatory or optional to this medal criteria.';
$string['silverquest'] = 'Number of Questionnaires';
$string['configdefaultsilverquest'] = 'Number of Questionnaires required for silver if used.';

$string['configsilverquiz'] = 'Quiz Criteria Type';
$string['configsilverquizxp'] = 'Define whether the minimum number of Quiz count should be excluded, mandatory or optional to this medal criteria.';
$string['silverquiz'] = 'Number of Quizzes';
$string['configdefaultsilverquiz'] = 'Number of Quizzes required for silver if used.';

$string['configsilverembed'] = 'Embed Criteria Type';
$string['configsilverembedxp'] = 'Define whether the minimum number of embedded videos count should be excluded, mandatory or optional to this medal criteria.';
$string['silverembed'] = 'Number of embedded videos';
$string['configdefaultsilverembed'] = 'Number of embedded videos required for silver if used.';

$string['configsilverchat'] = 'Chat Criteria Type';
$string['configsilverchatxp'] = 'Define whether the minimum number of chat activity count should be excluded, mandatory or optional to this medal criteria.';
$string['silverchat'] = 'Number of Chat Activities';
$string['configdefaultsilverchat'] = 'Number of chat activities required for silver if used.';

$string['configsilverforum'] = 'Forum Criteria Type';
$string['configsilverforumxp'] = 'Define whether the minimum number of forum count should be excluded, mandatory or optional to this medal criteria.';
$string['silverforum'] = 'Number of Forums';
$string['configdefaultsilverforum'] = 'Number of Forums required for silver if used.';

$string['configsilverwiki'] = 'Wiki Criteria Type';
$string['configsilverwikixp'] = 'Define whether the minimum number of Wiki\'s count should be excluded, mandatory or optional to this medal criteria.';
$string['silverwiki'] = 'Number of Wiki Activities';
$string['configdefaultsilverwiki'] = 'Number of Wiki Activities required for silver if used.';

$string['configsilverbook'] = 'Book Criteria Type';
$string['configsilverbookxp'] = 'Define whether the minimum number of Books should be excluded, mandatory or optional to this medal criteria.';
$string['silverbook'] = 'Number of Books';
$string['configdefaultsilverbook'] = 'Number of Books required for silver if used.';

$string['configsilverdatabase'] = 'Database Criteria Type';
$string['configsilverdatabasexp'] = 'Define whether the minimum number of databases should be excluded, mandatory or optional to this medal criteria.';
$string['silverdatabase'] = 'Number of Databases';
$string['configdefaultsilverdatabase'] = 'Number of Databases required for silver if used.';

$string['configsilverworkshop'] = 'Workshop Criteria Type';
$string['configsilverworkshopxp'] = 'Define whether the minimum number of workshops should be excluded, mandatory or optional to this medal criteria.';
$string['silverworkshop'] = 'Number of Workshops';
$string['configdefaultsilverworkshop'] = 'Number of Workshops required for silver if used.';

$string['configsilverchoice'] = 'Choice Criteria Type';
$string['configsilverchoicexp'] = 'Define whether the minimum number of Choice Activities should be excluded, mandatory or optional to this medal criteria.';
$string['silverchoice'] = 'Number of Choice Activities';
$string['configdefaultsilverchoice'] = 'Number of Choice activities required for silver if used.';

$string['configsilverglossary'] = 'Glossary Criteria Type';
$string['configsilverglossaryxp'] = 'Define whether the minimum number of glossaries should be excluded, mandatory or optional to this medal criteria.';
$string['silverglossary'] = 'Number of Glossaries';
$string['configdefaultsilverglossary'] = 'Number of Glossaries required for silver if used.';

//gold settings --------------------------------------------------------------

$string['goldnumoptional'] = "Optional Requirements";
$string['goldnumoptionalxp'] = "This is the number of optional settings needed to be fulfilled before awarding this 
medal. Use this setting when benchmarking to a selection of a range of activities. Note: select only the minimum number of criteria.";
$string['gold_heading'] = 'Configure Gold Benchmarking';
$string['explaingeneralgold'] = 'These are the settings used for the gold medal auto calculations. Note: a course must meet silver criteria before being assessed for gold criteria. ';

$string['configgoldresources'] = 'Resources Criteria Type';
$string['configgoldresourcesxp'] = 'Define whether the minimum resources count should be excluded, mandatory or optional to this medal criteria.';
$string['goldresources']= 'Number of Resources';
$string['configdefaultgoldresources'] = 'Number of resources required for gold if used. This is a total of documents within books,
labels, pages and on the main course page.';

$string['configgoldassignments'] = 'Assignments Criteria Type';
$string['configgoldassignmentsxp'] = 'Define whether the minimum number of assignments count should be excluded, mandatory or optional to this medal criteria.';
$string['goldassignments'] = 'Number of Assignments';
$string['configdefaultgoldassignments'] = 'Number of assignments required for gold if used. This is the total of assignments within a course page.';

$string['configgoldfeedback'] = 'Feedback Criteria Type';
$string['configgoldfeedbackxp'] = 'Define whether the minimum number of Feedback count should be excluded, mandatory or optional to this medal criteria.';
$string['goldfeedback'] = 'Number of Feedback Activities';
$string['configdefaultgoldfeedback'] = 'Number of Feedback activities required for gold if used.';

$string['configgoldims'] = 'IMS package Criteria Type';
$string['configgoldimsxp'] = 'Define whether the minimum number of IMS package count should be excluded, mandatory or optional to this medal criteria.';
$string['goldims'] = 'Number of IMS packages';
$string['configdefaultgoldims'] = 'Number of IMS packages required for gold if used.';

$string['configgoldquest'] = 'Questionnaire Criteria Type';
$string['configgoldquestxp'] = 'Define whether the minimum number of questionnaire count should be excluded, mandatory or optional to this medal criteria.';
$string['goldquest'] = 'Number of Questionnaires';
$string['configdefaultgoldquest'] = 'Number of Questionnaires required for gold if used.';

$string['configgoldquiz'] = 'Quiz Criteria Type';
$string['configgoldquizxp'] = 'Define whether the minimum number of Quiz count should be excluded, mandatory or optional to this medal criteria.';
$string['goldquiz'] = 'Number of Quizzes';
$string['configdefaultgoldquiz'] = 'Number of Quizzes required for gold if used.';

$string['configgoldembed'] = 'Embed Criteria Type';
$string['configgoldembedxp'] = 'Define whether the minimum number of embedded videos count should be excluded, mandatory or optional to this medal criteria.';
$string['goldembed'] = 'Number of embedded videos';
$string['configdefaultgoldembed'] = 'Number of embedded videos required for gold if used.';

$string['configgoldchat'] = 'Chat Criteria Type';
$string['configgoldchatxp'] = 'Define whether the minimum number of chat activity count should be excluded, mandatory or optional to this medal criteria.';
$string['goldchat'] = 'Number of Chat Activities';
$string['configdefaultgoldchat'] = 'Number of chat activities required for gold if used.';

$string['configgoldforum'] = 'Forum Criteria Type';
$string['configgoldforumxp'] = 'Define whether the minimum number of forum count should be excluded, mandatory or optional to this medal criteria.';
$string['goldforum'] = 'Number of Forums';
$string['configdefaultgoldforum'] = 'Number of Forums required for gold if used.';

$string['configgoldwiki'] = 'Wiki Criteria Type';
$string['configgoldwikixp'] = 'Define whether the minimum number of Wiki\'s count should be excluded, mandatory or optional to this medal criteria.';
$string['goldwiki'] = 'Number of Wiki Activities';
$string['configdefaultgoldwiki'] = 'Number of Wiki Activities required for gold if used.';

$string['configgoldbook'] = 'Book Criteria Type';
$string['configgoldbookxp'] = 'Define whether the minimum number of Books should be excluded, mandatory or optional to this medal criteria.';
$string['goldbook'] = 'Number of Books';
$string['configdefaultgoldbook'] = 'Number of Books required for gold if used.';

$string['configgolddatabase'] = 'Database Criteria Type';
$string['configgolddatabasexp'] = 'Define whether the minimum number of databases should be excluded, mandatory or optional to this medal criteria.';
$string['golddatabase'] = 'Number of Databases';
$string['configdefaultgolddatabase'] = 'Number of Databases required for gold if used.';

$string['configgoldworkshop'] = 'Workshop Criteria Type';
$string['configgoldworkshopxp'] = 'Define whether the minimum number of workshops should be excluded, mandatory or optional to this medal criteria.';
$string['goldworkshop'] = 'Number of Workshops';
$string['configdefaultgoldworkshop'] = 'Number of Workshops required for gold if used.';

$string['configgoldchoice'] = 'Choice Criteria Type';
$string['configgoldchoicexp'] = 'Define whether the minimum number of Choice Activities should be excluded, mandatory or optional to this medal criteria.';
$string['goldchoice'] = 'Number of Choice Activities';
$string['configdefaultgoldchoice'] = 'Number of Choice activities required for gold if used.';

$string['configgoldglossary'] = 'Glossary Criteria Type';
$string['configgoldglossaryxp'] = 'Define whether the minimum number of glossaries should be excluded, mandatory or optional to this medal criteria.';
$string['goldglossary'] = 'Number of Glossaries';
$string['configdefaultgoldglossary'] = 'Number of Glossaries required for gold if used.';
