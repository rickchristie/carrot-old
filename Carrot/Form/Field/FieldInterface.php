<?php

/**
 * This file is part of the Carrot framework.
 *
 * Copyright (c) 2011 Ricky Christie <seven.rchristie@gmail.com>.
 *
 * Licensed under the MIT License.
 *
 */

/**
 * Field Interface
 * 
 * 'Field' is defined as the object we use to extract variable(s)
 * we need from the user, via the form UI. Thus the field object
 * must be able to:
 *
 * <ul>
 *     <li>
 *         Render itself to HTML form string.
 *     </li>
 *     <li>
 *         Check if the form submission is valid, i.e. it contains
 *         variables the field needs.
 *     </li>
 *     <li>
 *         Get the submitted value from the form submission
 *         arrays.
 *     </li>
 *     <li>
 *         Set its own default value.
 *     </li>
 * </ul>
 * 
 * Implementations of this interface would be contained inside
 * \Carrot\Form\FormDefinition instance. Much of the methods
 * inside this interface is a contract between it and the form
 * definition instance.
 * 
 * Should you feel the need for it, you can write an
 * implementation of FieldInterface that renders itself to several
 * form input controls. You can have, as an example, a DateField
 * class that renders itself to three select boxes for date,
 * month, year, but returns an ISO-8601 date string when asked for
 * the submitted value. All rendering and processing of user input
 * into a variable that we need is encapsulated in this object.
 * 
 * It is to be noted that although the processing of one/several
 * HTML form controls into a variable is encapsulated in your
 * implementation, validation is not, as it should belong to the
 * domain layer of your application. You can, however, apply some
 * processes that is innate to the presentation layer itself. An
 * example of this is the aforementioned DateField example, where
 * the three select boxes are processed into an ISO-8601 date
 * string. Another example of this is an TextareXSSSecureField
 * that properly escapes the text input. Depending on the nature
 * of your application, the XSS filtering can belong to the
 * presentation layer, as the model shouldn't be aware that the
 * input is coming from a web form, and thus vulnerable to XSS.
 * 
 * @author      Ricky Christie <seven.rchristie@gmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace Carrot\Form\Field;

use Carrot\Message\ValidationErrorMessageInterface;

interface FieldInterface
{   
    /**
     * Get the field value from the form submission arrays.
     * 
     * Since the FieldInterface implementation is the only object that
     * knows how it is rendered into HTML form string, it is also the
     * only object that knows how to extract its value from the form
     * submission arrays.
     * 
     * In this method, you will have to process the form submission
     * arrays and return a value. Two array are given as arguments,
     * the $formSubmissionArray is quite literally, a form submission
     * array, and could be either GET or POST, depending on the form -
     * but this shouldn't concern your field object. The second array
     * is $fileSubmissionArray, which is the FILES array. You can
     * simply ignore this array if your field doesn't concern
     * uploading files.
     * 
     * So that calling this method would be perfectly safe, it is
     * expected that you check first that the form submission is
     * valid, via {@see isSubmissionValid()}. If it's not valid,
     * return the field's DEFAULT value, which it should always have.
     * For example, when we're writing a TextField implementation, the
     * default value will always be an empty string, but the default
     * value for a date field might differ.
     * 
     * @param array $formSubmissionArray Could be either $_GET or $_POST.
     * @param array $fileSubmissionArray The $_FILES array.
     * @return mixed The value from the submission arrays, or a default value. 
     * 
     */
    public function getValue(array $formSubmissionArray, array $fileSubmissionArray);
    
    /**
     * Checks if the form submission is valid.
     *
     * Since the FieldInterface implementation is the only object that
     * knows how it is rendered into HTML form string, it is also the
     * only object that knows whether the form submission is valid or
     * not. The checking process should be unique to each field, and
     * is closely related to the {@see getValue()} and {@see render()}
     * logic.
     * 
     * For example, a TextField can check if the form submission is
     * valid or not by checking if its 'name' attribute exists in the
     * form submission array, and is a string. If so, the TextField
     * should be able to process the form submission array to get the
     * field value {@see getValue()}, thus the form submission array
     * is considered valid.
     * 
     * If the array key doesn't exist, or the content is not a string,
     * it is then considered an invalid form submission by TextField -
     * because if the TextField is rendered correctly {@see render()},
     * the array key will always exist in the form submission array
     * and it will always contain a string, which is what
     * {@see getValue()} expected for processing.
     * 
     * The FormDefinition object will call this method on each field
     * to make sure that the form submission is completely valid
     * {@see \Carrot\Form\FormDefinition::isSubmissionValid()}.
     * 
     * You must return TRUE if the form submission arrays contains all
     * the information you need in field value processing AND the form
     * submission array's contents corresponds with your rendering
     * logic {@see render()}, which means {@see getValue()} can be
     * called safely. If you can't, return FALSE and the form
     * submission will be deemed as invalid.
     * 
     * @param array $formSubmissionArray Could be either $_GET or $_POST.
     * @param array $fileSubmissionArray The $_FILE array.
     * @return bool TRUE if the submission array is valid, FALSE otherwise.
     * 
     */
    public function isSubmissionValid(array $formSubmissionArray, array $fileSubmissionArray);
    
    /**
     * Set the default value for this field.
     * 
     * How you set the default value is up to you, however, it is
     * expected that the default value gets rendered back to the
     * client. This method is very useful in setting a default value
     * in edit forms and in retaining the previously filled values
     * when displaying a form error page.
     * 
     * The $defaultValue argument should be the same structure as the
     * value you returned in {@see getValue()}. Ideally, you should
     * have a private/protected method for validating the value. If
     * the default value given is invalid, you should return FALSE
     * without raising an exception. Otherwise, if the default value
     * is valid and the process is completed successfully, return
     * TRUE.
     * 
     * @param mixed $defaultValue The default value.
     * @return bool TRUE if default value set, FALSE if default value not set/invalid.
     * 
     */
    public function setDefaultValue($defaultValue);
    
    /**
     * Get the field ID string.
     * 
     * Each field should have an ID string, which would be unique in
     * each \Carrot\Form\FormDefinition. Ideally this should be set
     * in your constructor.
     * 
     * Your FieldInterface will always render itself to a one or more
     * form controls. Each form controls should have their own 'name'
     * attribute, which will be used as the name of the variable in
     * the form submission array (GET, POST, or FILES).
     * 
     * To prevent clash in this 'name' attribute, you MUST use field
     * ID as the 'name' attribute of your form controls. For example,
     * a TextField with the field ID 'username' will always render to
     * an input form control with 'username' as its 'name' attribute:
     *
     * <code>
     * <input type="text" name="username" ... />
     * </code>
     * 
     * If your FieldInterface implementation has more than one form
     * control, use the array syntax on the 'name' attribute. For
     * example, a CheckboxGroupField (which consists of more than one
     * checkbox form control) with the field ID 'hobbys' will always
     * render its form controls with 'hobbys[]' as their 'name'
     * attribute: 
     *
     * <code>
     * <input type="checkbox" name="hobbys[]" ... />
     * <input type="checkbox" name="hobbys[]" ... />
     * <input type="checkbox" name="hobbys[]" ... />
     * </code>
     *
     * If you do not use field ID as the 'name' attribute of your
     * form controls, there is a chance that the form submission
     * arrays might clash, since there are more than one field
     * accessing the same index on the form submission arrays.
     * 
     * @return string The field ID.
     *
     */
    public function getID();
    
    /**
     * Get field label string.
     * 
     * The label of the field. Since a field can consist of many HTML
     * controls, each with their own label, what you have to return
     * is the label for the entire field. Preferably the field label
     * should be set at object construction.
     * 
     * @return string The field label.
     *
     */
    public function getLabel();
    
    /**
     * Get the HTML ID to fill the 'for' attribute in the field label.
     * 
     * This method is useful for the renderer when it renders the
     * your field's label. It could use the HTML ID attribute to fill
     * the 'for' attribute in the label tag, as such:
     *
     * <code>
     * <label for="<?php echo $field->getForAttributeInLabel() ?>">
     *     <?php echo $field->getLabel() ?>
     * </label>
     * </code>
     * 
     * If your FieldInterface implementation consists of more than one
     * form controls and/or you don't want to the 'for' attribute of
     * the label to be set, tell it to the renderer by returning NULL.
     * 
     * @return string|NULL The HTML ID attribute or NULL.
     * 
     */
    public function getForAttributeInLabel();
    
    /**
     * Add a ValidationErrorMessageInterface instance.
     * 
     * Your FieldInterface implementation should store the error
     * messages and return it when called. This method will be called
     * by \Carrot\Form\FormDefinition, which could prove useful if
     * your FieldInterface implementation have to render error itself.
     * 
     * @param ValidationErrorMessageInterface $message The error message to add.
     * 
     */
    public function addErrorMessage(ValidationErrorMessageInterface $message);
    
    /**
     * Get ValidationErrorMessageInterface instances in an array.
     * 
     * Should return an array with this structure:
     *
     * <code>
     * $errorMessages = array(
     *     $validationErrorMessageA,
     *     $validationErrorMessageB,
     *     $validationErrorMessageC
     * );
     * </code>
     * 
     * @return array Contains ValidationErrorMessageInterface instances.
     * 
     */
    public function getErrorMessages();
    
    /**
     * Check if the field currently has error messages attached to it.
     *
     * This method is useful for renderers if it needs to render
     * fields with error messages differently.
     * 
     * @return bool TRUE if has error messages, FALSE otherwise.
     *
     */
    public function hasErrorMessages();
    
    /**
     * Returns TRUE if the field label should be rendered by the renderer.
     * 
     * Generally speaking, its hould be no problem for the renderer to
     * render the label of each field. However this might not be the
     * case for every FieldInterface implementations. The hidden input
     * fields, for example, doesn't need its label to be rendered.
     *
     * The object rendering the form should consult this method before
     * rendering the label (if not, they're awfully rude). Return TRUE
     * if your FieldInterface implementation allows them to be
     * rendered, FALSE otherwise.
     * 
     * @see shouldRendererRenderErrors()
     * @return bool TRUE if field label should be rendered, FALSE otherwise.
     * 
     */
    public function shouldRendererRenderLabel();
    
    /**
     * Returns TRUE if field errors should be rendered by the renderer.
     * 
     * Generally speaking, it should be no problem for the renderer to
     * render field specific errors. However this might not be the
     * case for every FieldInterface implementations. There might be a
     * case where rendering field specific errors are not required.
     * The FieldInterface implementation could be so complicated, for
     * example, that rendering field specific errors should be left
     * to the FieldInterface implementation itself.
     *
     * The object rendering the form should consult this method before
     * rendering field specific errors (if not, they're awfully rude).
     * Return TRUE if your FieldInterface implementation allows them
     * to be rendered, FALSE otherwise.
     * 
     * @see shouldRendererRenderLabel()
     * @return bool TRUE if the field errors should be rendered, FALSE otherwise.
     * 
     */
    public function shouldRendererRenderErrors();
    
    /**
     * Render the field control into HTML form string.
     * 
     * This method is responsible of rendering the FieldInterface
     * controls into HTML form tags. Please note that you must ESCAPE
     * variables properly. Other objects will that the string returned
     * from this method is PROPERLY ESCAPED and SAFE FOR OUTPUT.
     * 
     * NOTE: You have to render your form controls with the field ID
     * as their 'name' attribute {@see getID()} to avoid form
     * submission index clash with other fields.
     *
     * NOTE: If you wish to have the label's 'for' attribute set
     * correctly, {@see getForAttributeInLabel()}.
     * 
     * @return string The field control, rendered as a HTML string, properly escaped.
     * 
     */
    public function render();
}