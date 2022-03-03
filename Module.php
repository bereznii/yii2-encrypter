<?php

/**
 * Contains the module class used to have encrypt console commands.
 *
 * @copyright Copyright (c) 2022 Dmytro Bereznii
 * @package bereznii/yii2-encrypter
 * @author Dmytro Bereznii <bereznii.d@gmail.com>
 */

namespace bereznii\encrypter;

use yii\base\BootstrapInterface;

/**
 * Bootstrap the module to allow the use of the console commands.
 *
 * @author Dmytro Bereznii <bereznii.d@gmail.com>
 * @version 1.2
 * @property-read \bereznii\encrypter\components\Encrypter $encrypter
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * @return void
     */
    public function init()
    {
        $configFile = \Yii::getAlias('@app/config/encrypter.php');

        if (file_exists($configFile)) {
            $this->setComponents([
                'encrypter' => require($configFile),
            ]);
        }
        parent::init();
    }

    /**
     * @param $app
     * @return void
     */
    public function bootstrap($app)
    {
        if ($app instanceof \yii\console\Application) {

            $this->controllerNamespace = 'bereznii\encrypter\commands';
            $this->setAliases([
                '@bereznii/encrypter' =>  dirname(__FILE__),
            ]);
        }
    }
}