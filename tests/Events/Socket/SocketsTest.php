<?php

namespace Tests\Events\Socket;

use Tests\TestCase;
use App\Models\User;
use App\Models\Account;

use App\Events\Socket\RefetchEntity;
use App\Events\Socket\DownloadAvailable;

class SocketsTest extends TestCase
{
    private function fakeUser()
    {
        $account = new Account();
        $account->key = 'testkey';

        $user = new User();
        $user->id = 1;
        $user->account = $account;

        return $user;
    }

    // ------------------- RefetchEntity.php -------------------
    public function test_refetch_entity_event()
    {
        $user = $this->fakeUser();
        $entity = 'Invoice';
        $entityId = '123';

        $event = new RefetchEntity($entity, $entityId, $user);

        $this->assertSame($entity, $event->entity);
        $this->assertSame($entityId, $event->entity_id);
        $this->assertSame($user, $event->user);

        $channels = $event->broadcastOn();
        $this->assertCount(1, $channels);
        $this->assertEquals("private-user-{$user->account->key}-{$user->id}", $channels[0]->name);

        $payload = $event->broadcastWith();
        $this->assertSame(['entity' => $entity, 'entity_id' => $entityId], $payload);
    }

    // ------------------- DownloadAvailable.php -------------------
    public function test_download_available_event()
    {
        $user = $this->fakeUser();
        $url = 'https://example.com/file.pdf';
        $message = 'Download ready';

        $event = new DownloadAvailable($url, $message, $user);

        $this->assertSame($url, $event->url);
        $this->assertSame($message, $event->message);
        $this->assertSame($user, $event->user);

        $channels = $event->broadcastOn();
        $this->assertCount(1, $channels);
        $this->assertEquals("private-user-{$user->account->key}-{$user->id}", $channels[0]->name);

        $payload = $event->broadcastWith();
        $this->assertSame(['message' => $message, 'url' => $url], $payload);
    }
}

