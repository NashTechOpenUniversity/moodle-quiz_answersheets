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
 * The override qtype_multichoice_renderer for the quiz_answersheets module.
 *
 * @package   quiz_answersheets
 * @copyright 2020 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace quiz_answersheets\output\multichoice;

use html_writer;
use question_attempt;
use question_display_options;
use question_state;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/multichoice/renderer.php');

/**
 * The override qtype_multichoice_renderer for the quiz_answersheets module.
 *
 * @copyright  2020 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_multichoice_override_renderer extends \qtype_multichoice_single_renderer {

    /**
     * The code was copied from question/type/multichoice/renderer.php, with modifications.
     *
     * @param question_attempt $qa
     * @param question_display_options $options
     * @return string
     */
    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        $question = $qa->get_question();
        $response = $question->get_response($qa);

        $inputname = $qa->get_qt_field_name('answer');
        $inputattributes = [
            'type' => $this->get_input_type(),
            'name' => $inputname,
        ];

        if ($options->readonly) {
            $inputattributes['disabled'] = 'disabled';
        }

        $radiobuttons = [];
        $feedbackimg = [];
        $feedback = [];
        $classes = [];
        foreach ($question->get_order($qa) as $value => $ansid) {
            $ans = $question->answers[$ansid];
            $inputattributes['name'] = $this->get_input_name($qa, $value);
            $inputattributes['value'] = $this->get_input_value($value);
            $inputattributes['id'] = $this->get_input_id($qa, $value);
            $isselected = $question->is_choice_selected($response, $value);
            if ($isselected) {
                $inputattributes['checked'] = 'checked';
            } else {
                unset($inputattributes['checked']);
            }
            $hidden = '';
            if (!$options->readonly && $this->get_input_type() == 'checkbox') {
                $hidden = html_writer::empty_tag('input', [
                    'type' => 'hidden',
                    'name' => $inputattributes['name'],
                    'value' => 0,
                ]);
            }
            $choicenumber = html_writer::span(
                $this->number_in_style($value, $question->answernumbering), 'answernumber');
            $choicetext = $question->format_text(
                $ans->answer, $ans->answerformat, $qa, 'question', 'answer', $ansid);
            $choice = html_writer::div($choicetext, 'flex-fill ml-1');

            $radiobuttons[] = $hidden . html_writer::empty_tag('input', $inputattributes) .
                html_writer::tag('label', $choicenumber . $choice, [
                    'class' => 'd-flex w-auto ml-1',
                    'for' => $inputattributes['id'],
                ]);

            // Param $options->suppresschoicefeedback is a hack specific to the
            // oumultiresponse question type. It would be good to refactor to
            // avoid refering to it here.
            // Modification starts.
            /* Comment out core code.
            if ($options->feedback && empty($options->suppresschoicefeedback) &&
                    $isselected && trim($ans->feedback)) {
            */

            if ($options->feedback && empty($options->suppresschoicefeedback) &&
                    trim($ans->feedback)) {
                // Modification ends.
                $feedback[] = html_writer::tag('div',
                    $question->make_html_inline($question->format_text($ans->feedback, $ans->feedbackformat,
                        $qa, 'question', 'answerfeedback', $ansid)), ['class' => 'specificfeedback']);
            } else {
                $feedback[] = '';
            }
            $class = 'r' . ($value % 2);
            // Modification starts.
            /* Comment out core code.
            if ($options->correctness && $isselected) {
            */
            if ($options->correctness) {
                // Modification ends.
                $feedbackimg[] = $this->feedback_image($this->is_right($ans));
                $class .= ' ' . $this->feedback_class($this->is_right($ans));
            } else {
                $feedbackimg[] = '';
            }
            $classes[] = $class;
        }

        $result = '';
        $result .= html_writer::tag('div', $question->format_questiontext($qa), ['class' => 'qtext']);

        $result .= html_writer::start_tag('div', ['class' => 'ablock']);
        $result .= html_writer::tag('div', $this->prompt(), ['class' => 'prompt']);

        $result .= html_writer::start_tag('div', ['class' => 'answer']);
        foreach ($radiobuttons as $key => $radio) {
            $result .= html_writer::tag('div', $radio . ' ' . $feedbackimg[$key] . $feedback[$key],
                ['class' => $classes[$key]]) . "\n";
        }
        $result .= html_writer::end_tag('div'); // Answer.

        $result .= $this->after_choices($qa, $options);

        $result .= html_writer::end_tag('div'); // Ablock.

        if ($qa->get_state() == question_state::$invalid) {
            $result .= html_writer::nonempty_tag('div',
                    $question->get_validation_error($qa->get_last_qt_data()), ['class' => 'validationerror']);
        }

        return $result;
    }

}
