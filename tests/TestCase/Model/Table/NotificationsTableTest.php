<?php
declare(strict_types=1);

namespace Bakkerij\Notifier\Test\TestCase\Model\Table;

use Bakkerij\Notifier\Utility\NotificationManager;
use Cake\TestSuite\TestCase;

/**
 * Notifier\Model\Table\NotificationsTable Test Case
 */
class NotificationsTableTest extends TestCase
{
    public $fixtures = [
        'plugin.Bakkerij/Notifier.Notifications',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->Notifications = $this->getTableLocator()->get('Bakkerij/Notifier.Notifications');
    }

    public function tearDown(): void
    {
        unset($this->Notifications);

        parent::tearDown();
    }

    public function testEntity(): void
    {
        NotificationManager::instance()->addTemplate('newNotification', [
            'title' => 'New Notification',
            'body' => ':from has sent :to a notification about :about'
        ]);

        $notify = NotificationManager::instance()->notify([
            'users' => 1,
            'template' => 'newNotification',
            'vars' => [
                'from' => 'Bob',
                'to' => 'Leonardo',
                'about' => 'Programming Stuff'
            ]
        ]);

        $entity = $this->Notifications->get(2);

        $this->assertEquals('newNotification', $entity->template);
        $this->assertEquals('New Notification', $entity->title);
        $this->assertEquals('Bob has sent Leonardo a notification about Programming Stuff', $entity->body);
    }
}
