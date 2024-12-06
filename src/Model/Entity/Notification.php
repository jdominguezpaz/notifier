<?php
declare(strict_types=1);

namespace Bakkerij\Notifier\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;
use Cake\Utility\Text;

/**
 * Notification Entity.
 */
class Notification extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'template' => true,
        'vars' => true,
        'tracking_id' => true,
        'user_id' => true,
        'state' => false,
        'user' => false,
    ];

    /**
     * Virtual fields
     *
     * @var array<string>
     */
    protected $_virtual = ['title', 'body', 'unread', 'read'];

    /**
     * Getter for the vars-column.
     *
     * @param string|null $vars Data.
     * @return array|string|null
     */
    protected function _getVars(?string $vars): array|string|null
    {
        $array = json_decode($vars, true);

        if (is_object($array)) {
            return $array;
        }

        return $vars;
    }

    /**
     * Setter for the vars-column
     *
     * @param array|string|null $vars Data.
     * @return string|null
     */
    protected function _setVars(array|string|null $vars): ?string
    {
        if (is_array($vars)) {
            return json_encode($vars);
        }

        return $vars;
    }

    /**
     * Helper method to check if a string is valid JSON
     *
     * @param string $string The string to check
     * @return bool
     */
    private function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Getter for the title.
     * Data is used from the vars-column.
     * The template is used from the configurations.
     *
     * @return string
     */
    protected function _getTitle(): string
    {
        $templates = Configure::read('Notifier.templates');
        $template = $this->template ?? '';

        if (isset($templates[$template])) {
            $vars = json_decode($this->vars, true);
            return Text::insert($templates[$template]['title'], $vars);
        }

        return '';
    }

    /**
     * Getter for the body.
     * Data is used from the vars-column.
     * The template is used from the configurations.
     *
     * @return string
     */
    protected function _getBody(): string
    {
        $templates = Configure::read('Notifier.templates');
        $template = $this->template ?? '';
        if (isset($templates[$template])) {
            $vars = json_decode($this->vars, true);
            return Text::insert($templates[$template]['body'], $vars);
        }

        return '';
    }

    /**
     * Boolean if the notification is unread.
     *
     * @return bool
     */
    protected function _getUnread(): bool
    {
        return ($this->_properties['state'] ?? 0) === 1;
    }

    /**
     * Boolean if the notification is read.
     *
     * @return bool
     */
    protected function _getRead(): bool
    {
        return ($this->_properties['state'] ?? 1) === 0;
    }
}
