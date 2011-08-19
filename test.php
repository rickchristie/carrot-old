<?php

require __DIR__ . DIRECTORY_SEPARATOR . 'Carrot' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . 'Autoloader.php';
$autoloader = new Carrot\Core\Autoloader;
$autoloader->register();
require 'autoload.php';

$request = new Carrot\Core\Request(
    $_SERVER,
    $_GET,
    $_POST,
    $_FILES,
    $_COOKIE,
    $_REQUEST,
    $_ENV
);

$form = new Carrot\Form\FormDefinition;

$form->addField(new Carrot\Form\Field\TextField(
    'username',
    'User Name'
));

$form->addField(new Carrot\Form\Field\PasswordField(
    'password',
    'Password'
));

if ($form->isSubmissionValid($request))
{
    $chain = new Carrot\Validation\ValidationChain;
    $chain->setValues(array(
        'usernameBlah' => $form->getSubmittedValue('username', $request),
        'passwordBlah' => $form->getSubmittedValue('password', $request)
    ));
    
    $chain->start('usernameBlah')
          ->validate('existence.notEmpty')
          ->stop();
    
    $chain->start('passwordBlah')
          ->validate('existence.notEmpty')
          ->stop();
    
    if ($chain->passesValidation())
    {
        echo 'Passes Validation!';
    }
    else
    {
        echo 'Did not pass validation!';
        $messages = $chain->getMessages();
        $form->addValidatorMessages($messages, array(
            'usernameBlah' => 'username',
            'passwordBlah' => 'password'
        ));
        $form->setDefaultValues($request);
    }
}

// Render the form
$formRenderer = new Carrot\Form\FormRenderer($form);

?>

<form method="<?php echo $formRenderer->method() ?>" enctype="<?php $formRenderer->enctype() ?>" action="">
<?php echo $formRenderer->render() ?>
<button type="submit">
    Submit
</button>
</form>

<?php

exit;



$chain = new Carrot\Validation\ValidationChain;
$chain->setValues(array('username' => ''));

$chain->start('username')->validate('existence.notEmpty')->stop();

echo '<pre>', var_dump($chain->passesValidation()), '</pre>';
echo '<pre>', var_dump($messages = $chain->getMessages()), '</pre>';

$messages[0]->setFieldLabels(array('username' => 'User Name'));

echo '<pre>', var_dump($messages[0]->get()), '</pre>';

class Boo
{
    public function blah(&$heyho)
    {
        $this->heyho =& $heyho;
    }
    
    public function baz()
    {
        echo '<pre>', var_dump($this->heyho), '</pre>';
    }
}

class Foo
{
    public function __construct($string)
    {
        $this->string = $string;
    }
}

$a = array(
    new Foo(1),
    new Foo(2),
    new Foo(3)
);
$boo = new Boo;
$boo->blah($a);
$a[] = new Foo(4);
$boo->baz();

exit;

/**
 * SPL Autoload Register always gives you the fully qualified namespace
 * as the argument (without backslash prefix).
 *
 */

//namespace Foo;

spl_autoload_register(function($className)
{
    echo '<pre>', var_dump($className), '</pre>';
});

$blah = new \Heyho\Blah();