<?php
/**
 * Contains the controller class triggered by the ```./yii encrypter/decrypt```
 * console command.
 *
 * @link http://www.creationgears.com/
 * @copyright Copyright (c) 2015 Nicola Puddu
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package bereznii/yii2-encrypter
 * @author Dmytro Bereznii <bereznii.d@gmail.com>
 */

namespace bereznii\encrypter\controllers;

use yii\console\Controller;
use yii\helpers\Console;

/**
 * Decrypt a string using your current encrypter configuration.
 *
 * @author Dmytro Bereznii <bereznii.d@gmail.com>
 * @version 1.0
 * @since 1.1.0
 * @property-read \bereznii\encrypter\Module $module
 */
class DecryptController extends Controller
{
    /**
     * Decrypts a string using your current encrypter configuration.
     */
    public function actionIndex()
    {
        $decryptedString = $this->getEncrypter()->decrypt($this->prompt("\nType here the string to decrypt:"));

        $this->stdout("\nDecrypted String:\n");
        $this->stdout($decryptedString, Console::FG_GREEN);
        $this->stdout("\n\n");
    }

    /**
     * Returns the current instance of the encrypter component.
     *
     * @return \bereznii\encrypter\components\Encrypter
     */
    private function getEncrypter()
    {
        try {
            return $this->module->encrypter;
        } catch (\Exception $exc) {
            $this->stdout("The encrypter is not set.\n", Console::FG_RED);
        }
    }

}
