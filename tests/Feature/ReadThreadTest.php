<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReadThreadTest extends TestCase
{
    use DatabaseMigrations;
    protected $thread;
    /**
     * A basic test example.
     * @test
     * @return void
     */

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->thread = factory('App\Thread')->create();
    }
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function a_user_can_browse_threads()
    {

       $this->get('/threads')
            ->assertSee($this->thread->title);
    }
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function a_user_can_view_single_thread(){
         $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function a_user_can_read_replies_that_are_associated_with_a_thread(){
        //given a thread with a replies
        // then we see the thread and replies
        $reply= factory('App\Reply')->create(['thread_id'=>$this->thread->id]);
        $this->get($this->thread->path())
            ->assertSee($reply->body);
    }

    /**
     * A basic test example.
     * @test
     * @return void
     */

    public function a_user_can_filter_threads_according_to_tag(){
       // $this->withExceptionHandling();
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread',['channel_id'=>$channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get('/threads/'.$channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);

    }

    /**
     * A basic test example.
     * @test
     * @return void
     */

    public function a_user_can_filter_threads_by_any_username(){
        $this->signIn(create('App\User',['name'=>'JohnDoe']));

        $threadByAuth = create('App\Thread',['user_id'=>auth()->id()]);
        $threadNotByAuth = create('App\Thread');
        $this->get('threads?by=JohnDoe')
            ->assertSee($threadByAuth->title)
            ->assertDontSee($threadNotByAuth->title);
    }
    /**
     * A basic test example.
     * @test
     * @return void
     */
    public function a_user_can_filter_threads_by_popularity(){
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply',['thread_id'=>$threadWithTwoReplies->id],2);
        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id'=>$threadWithThreeReplies->id]);
        $threadWithNoReplies = $this->thread;
        $response = $this->getJson('threads?popular=1')->json();
        $this->assertEquals([3,2,0], array_column($response,'replies_count'));
    }
}
