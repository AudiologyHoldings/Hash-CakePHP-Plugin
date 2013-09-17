Hash Plugin for CakePHP 2x
=================

This is a very simple utility plugin to allow the easy standardization of
creating unique hashes for various inputs
(dynamically derrived as well as explicitly)

Install
-------------

```
git submodule add git@github.com:AudiologyHoldings/Hash-CakePHP-Plugin.git app/Plugin/Hash
```

then edit your `bootstrap.php` to load the plugin:

```
CakePlugin::load('Hash');
```

Usage
-------------

In `AppController` (or any Controller)

```
$components = array( 'Hash.Hash' );
helpers = array( 'Hash.Hash' );
```

In your View

```
echo $this->Hash->hiddenFormVerify();
  /* same as */
echo $this->Form->hidden('Form.verify', array('value' => $this->Hash->formHash()));
```

In your Controller, to see if data was submitted correctly

```
if ($this->Hash->verifyFormDataHash()) {
	// form was submitted with value hash value
}
```
