<?php
/**
 * The main videofile configuration form.
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_videofile
 * @copyright  2013 Jonas Nockert <jonasnockert@gmail.com>, 2018 Niels Seidel <niels.seidel@nise81.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->libdir . '/filelib.php');

class mod_videofile_mod_form extends moodleform_mod {
    /**
     * Defines the videofile instance configuration form.
     *
     * @return void
     */
    public function definition() {
        global $CFG;

        $config = get_config('videofile');
        $mform =& $this->_form;

        // Name and description fields.
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size' => '48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name',
                        get_string('maximumchars', '', 255),
                        'maxlength',
                        255,
                        'client');
        $this->standard_intro_elements(false);

        // Video fields.
        $mform->addElement('header',
                           'video_fieldset',
                           get_string('video_fieldset', 'videofile'));

        // Width.
        $mform->addElement('text',
                           'width',
                           get_string('width', 'videofile'),
                           array('size' => 4));
        $mform->setType('width', PARAM_INT);
        $mform->addHelpButton('width', 'width', 'videofile');
        $mform->addRule('width', null, 'required', null, 'client');
        $mform->addRule('width', null, 'numeric', null, 'client');
        $mform->addRule('width', null, 'nonzero', null, 'client');
        $mform->setDefault('width', $config->width);

        // Height.
        $mform->addElement('text',
                           'height',
                           get_string('height', 'videofile'),
                           array('size' => 4));
        $mform->setType('height', PARAM_INT);
        $mform->addHelpButton('height', 'height', 'videofile');
        $mform->addRule('height', null, 'required', null, 'client');
        $mform->addRule('height', null, 'numeric', null, 'client');
        $mform->addRule('height', null, 'nonzero', null, 'client');
        $mform->setDefault('height', $config->height);

        // Responsive.
        $mform->addElement('advcheckbox',
                           'responsive',
                           get_string('responsive', 'videofile'),
                           get_string('responsive_label', 'videofile'));
        $mform->setType('responsive', PARAM_INT);
        $mform->addHelpButton('responsive', 'responsive', 'videofile');
        $mform->setDefault('responsive', $config->responsive);

        // Video file manager.
        $options = array('subdirs' => false,
                         'maxbytes' => 0,
                         'maxfiles' => -1,
                         'accepted_types' => array('.mp4', '.webm', '.ogv'));
        $mform->addElement(
            'filemanager',
            'videos',
            get_string('videos', 'videofile'),
            null,
            $options);
        $mform->addHelpButton('videos', 'videos', 'videofile');
        $mform->addRule('videos', null, 'required', null, 'client');

        // Posters file manager.
        $options = array('subdirs' => false,
                         'maxbytes' => 0,
                         'maxfiles' => 1,
                         'accepted_types' => array('image'));
        $mform->addElement(
            'filemanager',
            'posters',
            get_string('posters', 'videofile'),
            null,
            $options);
        $mform->addHelpButton('posters', 'posters', 'videofile');

        // Captions file manager.
        $options = array('subdirs' => false,
                         'maxbytes' => 0,
                         'maxfiles' => -1,
                         'accepted_types' => array('.vtt'));
        $mform->addElement(
            'filemanager',
            'captions',
            get_string('captions', 'videofile'),
            null,
            $options);
        $mform->addHelpButton('captions', 'captions', 'videofile');



        
        // Dublin Core fields.
        $mform->addElement('header','fielddubline', get_string('fieldsetdc', 'videofile'));
        $mform->setExpanded('fielddubline');

        // creator.
        $mform->addElement('text', 'creator', get_string('creator', 'videofile'), array('size' => 30));
        $mform->setType('creator', PARAM_TEXT);
        
        // subject.
        $mform->addElement('text', 'subject', get_string('subject', 'videofile'), array('size' => 30));
        $mform->setType('subject', PARAM_TEXT);
        
        // publisher.
        $mform->addElement('text', 'publisher', get_string('publisher', 'videofile'), array('size' => 30));
        $mform->setType('publisher', PARAM_TEXT);
        
        // contributor.
        $mform->addElement('text', 'contributor', get_string('contributor', 'videofile'), array('size' => 30));
        $mform->setType('contributor', PARAM_TEXT);
        
        // date.
        $mform->addElement('text', 'date', get_string('date', 'videofile'), array('size' => 30));
        $mform->setType('date', PARAM_INT);

        // source.
        $mform->addElement('text', 'source', get_string('source', 'videofile'), array('size' => 30));
        $mform->setType('source', PARAM_TEXT);

        // language.
        $mform->addElement('text', 'language', get_string('language', 'videofile'), array('size' => 30));
        $mform->setType('language', PARAM_TEXT);

        // relation.
        $mform->addElement('text', 'relation', get_string('relation', 'videofile'), array('size' => 30));
        $mform->setType('relation', PARAM_TEXT);

        // coverage.
        $mform->addElement('text', 'coverage', get_string('coverage', 'videofile'), array('size' => 30));
        $mform->setType('coverage', PARAM_TEXT);

        // rights. xxx
        $mform->addElement('text', 'right', get_string('rights', 'videofile'), array('size' => 30));
        $mform->setType('rights', PARAM_TEXT);

        // license.
          $options = array(
            'ccby' => "Creative Commons CC-BY",
            'pd' => "Public Domain",
            'r' => "Rights Reserved"
        );
        $mform->addElement('select', 'license', get_string('license', 'videofile'), $options);
        $mform->getElement('license')->setMultiple(false);
        $mform->getElement('license')->setSelected(array('r'));

        // institution.
        $mform->addElement('text', 'institution', get_string('institution', 'videofile'), array('size' => 30));
        $mform->setType('institution', PARAM_TEXT);

        // tags.
        //$mform->addElement('text', 'tags', get_string('tags', 'videofile'), array('size' => 4));
        //$mform->setType('tags', PARAM_TEXT);




        // Video fields.
        $mform->addElement('header','fieldsetdidactic',get_string('fieldsetdidactic', 'videofile'));
        $mform->setExpanded('fieldsetdidactic');

        // sorts
        $options = array(
            'football' => "Fußball",
            'handball' => "Handball",
            'barren' => "Barren",
            'judo' => "Judo",
            'volleyball' => "Volleyball",
            'athletics' => "Leichtathletik"
        );
        $mform->addElement('select', 'sports', get_string('sports', 'videofile'), $options);
        $mform->getElement('sports')->setMultiple(false);
        //$mform->getElement('sports')->setSelected(array('val1', 'val2'));


        // competencies
        $options = array(
            'movement' => "Bewegen und Handeln",
            'reflexion' => "Reflektieren und Urteilen",
            'intercation' => "Interagieren",
            'methods' => "Methoden anwenden"
        );
        $mform->addElement('select', 'compentencies', get_string('compentencies', 'videofile'), $options);
        $mform->getElement('compentencies')->setMultiple(true);
        //if(isset($CFG->videofile_compentencies)){
           // $mform->setDefault('compentencies', $onfig->compentencies);
        //}
        file_put_contents('php://stderr', print_r($config, TRUE));

        // courselevel
        $options = array(
            'eingangsstufe' =>  "Eingangsstufe",
            'unterstufe' =>  "Unterstufe",
            'mittelstufe' =>  "Mittelstufe",
            'werkstufe' =>  "Werkstufe",
            'k1' =>  "Klassenstufe 1",
            'k2' =>  "Klassenstufe 2",
            'k3' =>  "Klassenstufe 3",
            'k4' =>  "Klassenstufe 4",
            'k5' =>  "Klassenstufe 5",
            'k6' =>  "Klassenstufe 6",
            'k7' =>  "Klassenstufe 7",
            'k8' =>  "Klassenstufe 8",
            'k9' =>  "Klassenstufe 9",
            'k10' =>  "Klassenstufe 10",
            'k11' =>  "Klassenstufe 11",
            'k12' =>  "Klassenstufe 12",
            'k13' =>  "Klassenstufe 13"
        );
        $mform->addElement('select', 'courselevel', get_string('courselevel', 'videofile'), $options);
        $mform->getElement('courselevel')->setMultiple(true);

        // movements
        $options = array(
            'lsws' => "Laufen, Springen, Werfen, Stoßen",
            'games' => "Spiele",
            'ass' => "Bewegung an Geräten",
            'fight' => "Kämpfen nach Regeln",
            'moves' => "Bewegungsfolgen gestalten und darstellen",
            'water' => "Bewegen im Wasser",
            'cycling' => "Fahren, Rollen, Gleiten"
        );
        $mform->addElement('select', 'movements', get_string('movements', 'videofile'), $options);
        $mform->getElement('movements')->setMultiple(false);

        // activities
        $options = array(
            'action1' => "Abbauen",
            'action2' => "Aufbauen",
            'action3' => "Begründen",
            'action4' => "Beraten",
            'action5' => "Beschreiben",
            'action6' => "Besprechen",
            'action7' => "Beurteilen",
            'action8' => "Demonstrieren",
            'action9' => "Disziplinieren",
            'action10' => "Erklären",
            'action11' => "Feedback, Korrektur",
            'action12' => "Gesprächsrunde",
            'action13' => "Gruppenbildung",
            'action14' => "Helfen",
            'action15' => "Kooperieren",
            'action16' => "Medieneinsatz",
            'action17' => "Motivieren",
            'action18' => "Organisieren",
            'action19' => "Präsentieren",
            'action20' => "Sichern",
            'action21' => "Störung",
            'action22' => "Üben"
        );
        $mform->addElement('select', 'activities', get_string('activities', 'videofile'), $options);
        $mform->getElement('activities')->setMultiple(true);

        // actors
        $options = array(
            'teacher' => "Lehrer/in",
            'pupil' => "Schüler/in"
        );
        $mform->addElement('select', 'actors', get_string('actors', 'videofile'), $options);
        $mform->getElement('actors')->setMultiple(false);

        // perspectives
        $options = array(
            'persp1' => "Leistung",
            'persp2' => "Wagnis",
            'persp3' => "Gestaltung",
            'persp4' => "Körpererfahrung",
            'persp5' => "Kooperation",
            'persp6' => "Gesundheit"
        );
        $mform->addElement('select', 'perspectives', get_string('perspectives', 'videofile'), $options);
        $mform->getElement('perspectives')->setMultiple(true);

        // location
        $options = array(
            'hall' => "Sporthalle",
            'pool' => "Schwimmhalle",
            'outdoor' => "Outdoor"
        );
        $mform->addElement('select', 'location', get_string('location', 'videofile'), $options);
        $mform->getElement('location')->setMultiple(false);

        /*
type
mimetype 
format
filename 
length TYPE="int" LENGTH="8"  
size TYPE="int" LENGTH="10"   

// poster


         */

         // Standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Standard buttons, common to all modules.
        $this->add_action_buttons();
    }

    /**
     * Prepares the form before data are set.
     *
     * @param array $data to be set
     * @return void
     */
    public function data_preprocessing(&$defaultvalues) {
        if ($this->current->instance) {
            $options = array('subdirs' => false,
                             'maxbytes' => 0,
                             'maxfiles' => -1);
            $draftitemid = file_get_submitted_draft_itemid('videos');
            file_prepare_draft_area($draftitemid,
                                    $this->context->id,
                                    'mod_videofile',
                                    'videos',
                                    0,
                                    $options);
            $defaultvalues['videos'] = $draftitemid;

            $options = array('subdirs' => false,
                             'maxbytes' => 0,
                             'maxfiles' => 1);
            $draftitemid = file_get_submitted_draft_itemid('posters');
            file_prepare_draft_area($draftitemid,
                                    $this->context->id,
                                    'mod_videofile',
                                    'posters',
                                    0,
                                    $options);
            $defaultvalues['posters'] = $draftitemid;

            $options = array('subdirs' => false,
                             'maxbytes' => 0,
                             'maxfiles' => -1);
            $draftitemid = file_get_submitted_draft_itemid('captions');
            file_prepare_draft_area($draftitemid,
                                    $this->context->id,
                                    'mod_videofile',
                                    'captions',
                                    0,
                                    $options);
            $defaultvalues['captions'] = $draftitemid;

            if (empty($defaultvalues['width'])) {
                $defaultvalues['width'] = 800;
            }

            if (empty($defaultvalues['height'])) {
                $defaultvalues['height'] = 500;
            }
        }
    }

    /**
     * Validates the form input
     *
     * @param array $data submitted data
     * @param array $files submitted files
     * @return array eventual errors indexed by the field name
     */
    public function validation($data, $files) {
        $errors = array();

        if ($data['width'] <= 0) {
            $errors['width'] = get_string('err_positive', 'videofile');
        }

        if ($data['height'] <= 0) {
            $errors['height'] = get_string('err_positive', 'videofile');
        }

        return $errors;
    }
}
