<?php

namespace Tests\Feature;

use App\Services\Implementations\TestProhibitedWordsList;
use App\Services\Implementations\TestTrustedDomains;
use App\Services\Interfaces\ProhibitedWordsList;
use App\Services\Interfaces\TrustedDomains;
use App\Services\Interfaces\UserModificationLog;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use function PHPUnit\Framework\assertEquals;

class ExampleTest extends TestCase
{

    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        assertEquals(0, User::all()->count());
        $user = new User([
            'name' => 'username',
            'email' => 'asfd@sdf.com',
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
        assertEquals(1, User::all()->count());
    }

    public function test_user_can_be_deleted()
    {
        $user = new User([
            'name' => 'username',
            'email' => 'asfd@sdf.com',
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
        assertEquals(1, User::all()->count());
        $user->delete();
        assertEquals(0, User::all()->count());
    }

    public function test_user_can_be_updated()
    {
        $notes = 'asfadsf';
        $user = new User([
            'name' => 'username',
            'email' => 'asfd@sdf.com',
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
        assertEquals(0, User::where('notes', $notes)->count());
        $user = User::where('name', 'username')->first();
        /** @var User $user */
        $user->update(['notes' => $notes]);
//        assertEquals(1, User::where('notes', $notes)->count());
//        assertEquals(1, User::all()->count());
    }

    public function test_user_name_must_have_only_alphanumeric_characters()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The name format is invalid');
        $user = new User([
            'name' => '&username',
            'email' => 'asfd@sdf.com',
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
    }

    public function test_user_name_cant_be_shorter_than_8_characters()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The name must be at least 8 characters');
        $user = new User([
            'name' => 'user',
            'email' => 'asfd@sdf.com',
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
    }

    public function test_user_name_must_be_unique()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The name has already been taken');
        $user = new User([
            'name' => 'user1234',
            'email' => '1111@sdf.com',
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
        $user = new User([
            'name' => 'user1234',
            'email' => '2222@sdf.com',
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
    }

    public function test_user_name_cant_contain_prohibited_words()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The name contains a prohibited word');
        $this->app->singleton(
            ProhibitedWordsList::class,
            TestProhibitedWordsList::class
        );
        $user = new User([
            'name' => TestProhibitedWordsList::THE_WORD . 'person',
            'email' => 'asdf@sdf.com',
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
    }

    public function test_user_email_must_have_valid_format()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The email must be a valid email address');
        $user = new User([
            'name' => 'username',
            'email' => 'asfdsdf.com',
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
    }

    public function test_user_email_must_be_unique()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The email has already been taken');
        $email = 'asf@dsdf.com';
        $user = new User([
            'name' => 'username1',
            'email' => $email,
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
        $user = new User([
            'name' => 'username2',
            'email' => $email,
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
    }

    public function test_user_email_must_be_on_trusted_domain()
    {
        $this->app->singleton(
            TrustedDomains::class,
            TestTrustedDomains::class
        );
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The email domain is not trusted');
        $user = new User([
            'name' => 'username1',
            'email' => 'asf@' . TestTrustedDomains::THE_BAD_DOMAIN,
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
    }

    public function test_user_deletion_date_cant_be_earlier_than_creation_date()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The deleted must be a date after');
        $user = new User([
            'name' => 'username1',
            'email' => 'asf@google.com',
            'created' => Carbon::now('UTC'),
            'deleted' => Carbon::now('UTC')->subYears(1),
        ]);
        $user->save();
    }

    public function test_user_deletion_date_can_be_after_than_creation_date()
    {
        $user = new User([
            'name' => 'username1',
            'email' => 'asf@google.com',
            'created' => Carbon::now('UTC'),
            'deleted' => Carbon::now('UTC')->addYears(1),
        ]);
        $user->save();
        $this->assertTrue($user->deleted->greaterThanOrEqualTo($user->created));
    }

    public function test_user_remains_in_db_when_soft_deleted()
    {
        $this->assertEquals(0, User::withTrashed()->count());
        $user = new User([
            'name' => 'username1',
            'email' => 'asf@google.com',
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
        $this->assertNull($user->deleted);
        $this->assertEquals(1, User::withTrashed()->count());
        $user->delete();
        $this->assertEquals(1, User::withTrashed()->count());
        $user->refresh();
        $this->assertNotNull($user->deleted);
        $this->assertTrue($user->deleted->greaterThanOrEqualTo($user->created));
    }

    public function test_user_creation_is_journaled()
    {
        $log = $this->mock(UserModificationLog::class);
        $log->shouldReceive('logCreation')->times(1);
        $this->app->singleton(
            UserModificationLog::class,
            function() use ($log) { return $log; }
        );
        $user = new User([
            'name' => 'username1',
            'email' => 'asf@google.com',
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
    }

    public function test_user_update_is_journaled()
    {
        $log = $this->mock(UserModificationLog::class);
        $log->shouldReceive('logUpdate')->times(1);
        $log->shouldIgnoreMissing();
        $this->app->singleton(
            UserModificationLog::class,
            function() use ($log) { return $log; }
        );
        $user = new User([
            'name' => 'username1',
            'email' => 'asf@google.com',
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
        $user->notes = "adsf";
        $user->save();
    }

    public function test_user_deletion_is_journaled()
    {
        $log = $this->mock(UserModificationLog::class);
        $log->shouldReceive('logDeletion')->times(1);
        $log->shouldIgnoreMissing();
        $this->app->singleton(
            UserModificationLog::class,
            function() use ($log) { return $log; }
        );
        $user = new User([
            'name' => 'username1',
            'email' => 'asf@google.com',
            'created' => Carbon::now('UTC'),
        ]);
        $user->save();
        $user->delete();
    }

}
