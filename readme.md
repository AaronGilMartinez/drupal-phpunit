# Requisitos

- ddev

# Instalación

1. ddev add-on get ddev/ddev-selenium-standalone-chrome
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

## Test functionales

Clase padre: __BrowserTestBase__
Elementos mínimos del test funcional

- Módulos a instalar: propiedad de clase $modules
- Tema usado: propiedad de clase $defaultTheme
- Un test.
- Una aserción: al menos hay que llamar al método Assert() una vez.

$this->assertSession() para comprobar elementos en la página!

## Test functionales javasctipt

Clase padre: __WebDriverTestBase__
Elementos mínimos del test funcional

- Módulos a instalar: propiedad de clase $modules
- Tema usado: propiedad de clase $defaultTheme
- Un test.
- Una aserción: al menos hay que llamar al método Assert() una vez.

Cuidado con los elementos que requieren ser visibles o no aparecen/desaparecen de forma inmediata!

## Test Kernel

Clase padre: __KernelTestBase__
Elementos mínimos del test funcional

- Un test.
- Una aserción: al menos hay que llamar al método Assert() una vez.

La instalación o el uso de ciertas funcionalidades, requerirá la instalación “manual” de módulos, entidades, schemas y configuraciones!
