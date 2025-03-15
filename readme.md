# Requisitos

- ddev

# Instalación

2. ddev start
3. ddev composer install
5. ddev drush site:install -y
6. ddev drush en rsvplist -y

# Conocimento básico

- ¿Cómo son los test? __Son clases con el sufijo Test con métodos públicos con el prefijo test.__
  - Ejemplo: __SettingsFormTest.php_ y _testForm()__
- Ruta: __tests/src/TipoDeTest -> Drupal\my_module\tests\TipoDeTest__
- ¿Cómo Verificamos que nuestro código? __Con assert(). La aserción lanzará una excepción si no es correcta y dará un resultado de fallo.__
  - Ejemplo: __$this->assertEquals(1, $node->id());__

Lanzar PHPUnit: __ddev exec ./vendor/bin/phpunit__

# Test functionales

Clase padre: __BrowserTestBase__
Elementos mínimos del test funcional

- Módulos a instalar: propiedad de clase $modules
- Tema usado: propiedad de clase $defaultTheme
- Un test.
- Una aserción: al menos hay que llamar al método Assert() una vez.

$this->assertSession() para comprobar elementos en la página!
