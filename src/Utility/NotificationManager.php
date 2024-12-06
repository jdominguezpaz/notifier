<?php
declare(strict_types=1);

namespace Bakkerij\Notifier\Utility;

use Cake\Core\Configure;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Text;

/**
 * Notifier utility class
 */
class NotificationManager
{
    use LocatorAwareTrait;

    /**
     * @var NotificationManager|null
     */
    protected static ?NotificationManager $_generalManager = null;

    /**
     * Instance method for singleton pattern
     *
     * @param NotificationManager|null $manager Optional different manager (Helpful for testing).
     * @return NotificationManager
     */
    public static function instance(?NotificationManager $manager = null): NotificationManager
    {
        if ($manager instanceof NotificationManager) {
            static::$_generalManager = $manager;
        }
        if (empty(static::$_generalManager)) {
            static::$_generalManager = new NotificationManager();
        }
        return static::$_generalManager;
    }

    /**
     * Sends notifications to specific users.
     *
     * @param array $data Data with options.
     * @return string The tracking_id to follow the notification.
     */
    public function notify(array $data): string
    {
        $model = $this->getTableLocator()->get('Bakkerij/Notifier.Notifications');

        $_data = [
            'users' => [],
            'recipientLists' => [],
            'template' => 'default',
            'vars' => [],
            'tracking_id' => $this->getTrackingId()
        ];

        $data = array_merge($_data, $data);

        foreach ((array)$data['recipientLists'] as $recipientList) {
            $list = (array)$this->getRecipientList($recipientList);
            $data['users'] = array_merge($data['users'], $list);
        }

        foreach ((array)$data['users'] as $user) {
            $entity = $model->newEmptyEntity();

            $entity->set('template', $data['template']);
            $entity->set('tracking_id', $data['tracking_id']);
            $entity->set('vars', $data['vars']);
            $entity->set('state', 1);
            $entity->set('user_id', $user);
            $entity->set('unread', 1);
            $model->save($entity);
        }

        return $data['tracking_id'];
    }

    /**
     * Add a new recipient list
     *
     * @param string $name Name of the list.
     * @param array $userIds Array with id's of users.
     * @return void
     */
    public function addRecipientList(string $name, array $userIds): void
    {
        Configure::write('Notifier.recipientLists.' . $name, $userIds);
    }

    /**
     * Get a recipient list
     *
     * @param string $name The name of the list.
     * @return array|null
     */
    public function getRecipientList(string $name): ?array
    {
        return Configure::read('Notifier.recipientLists.' . $name);
    }

    /**
     * Add a notification template
     *
     * @param string $name Unique name.
     * @param array $options Options.
     * @return void
     */
    public function addTemplate(string $name, array $options = []): void
    {
        $_options = [
            'title' => 'Notification',
            'body' => '',
        ];

        $options = array_merge($_options, $options);

        Configure::write('Notifier.templates.' . $name, $options);
    }

    /**
     * Get a template
     *
     * @param string $name Name of the template.
     * @param string|null $type The type like `title` or `body`.
     * @return array|string|bool
     */
    public function getTemplate(string $name, ?string $type = null)
    {
        $templates = Configure::read('Notifier.templates');

        if (isset($templates[$name])) {
            if ($type === 'title') {
                return $templates[$name]['title'];
            }
            if ($type === 'body') {
                return $templates[$name]['body'];
            }
            return $templates[$name];
        }

        return false;
    }

    /**
     * Generate a tracking id
     *
     * @return string
     */
    public function getTrackingId(): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $trackingId = '';
        for ($i = 0; $i < 10; $i++) {
            $trackingId .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $trackingId;
    }
}
