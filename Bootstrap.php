<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

class Shopware_Plugins_Core_SwagFixDeprecatedTlsInSmtp_Bootstrap extends Shopware_Components_Plugin_Bootstrap
{
    /**
     * @var bool
     */
    private $hasSetIncludePath;

    public function getLabel()
    {
        return 'SwagFixDeprecatedTlsInSmtp';
    }

    public function getVersion()
    {
        return '1.0.0';
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return array(
            'version' => $this->getVersion(),
            'label' => $this->getLabel(),
            'description' => 'Fixes issues with TLS 1.0 not being supported anymore by some SMTP mail servers'
        );
    }

    public function install()
    {
        // We need some subscribes on old plugin system, to get called very early
        $this->subscribeEvent('Enlight_Bootstrap_AfterInitResource_models', 'replaceProtocolClass');
        $this->subscribeEvent('Enlight_Bootstrap_AfterInitResource_dbal_connection', 'replaceProtocolClass');
        $this->subscribeEvent('Enlight_Bootstrap_AfterInitResource_db', 'replaceProtocolClass');
        $this->subscribeEvent('Enlight_Bootstrap_InitResource_mail', 'replaceProtocolClass');
        $this->subscribeEvent('Enlight_Bootstrap_InitResource_mail_factory', 'replaceProtocolClass');
        $this->subscribeEvent('Enlight_Bootstrap_InitResource_template', 'replaceProtocolClass');
        $this->subscribeEvent('Enlight_Bootstrap_InitResource_template_factory', 'replaceProtocolClass');

        return true;
    }

    public function replaceProtocolClass(Enlight_Event_EventArgs $args)
    {
        // Patched in this version
        if ($this->assertMinimumVersion('5.4.5')) {
            return;
        }

        // Do not execute set_include_path often
        if ($this->hasSetIncludePath) {
            return;
        }

        // We removed include_path support in Zend in Shopware 5.3
        if ($this->assertMinimumVersion('5.3.0')) {
            require_once __DIR__ . '/Zend/Mail/Protocol/Smtp.php';
            $this->hasSetIncludePath = true;
            return;
        }

        $path = get_include_path();

        $path = __DIR__ . ':' . $path;
        set_include_path($path);

        $this->hasSetIncludePath = true;
    }
}