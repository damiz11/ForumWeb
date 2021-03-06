<?php

namespace Tests\Unit;


use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     * */
    protected $thread;
    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->thread = factory('App\Thread')->create();
    }
    /**
     * @test
     * */
    public function aThreadCanMakeAPath(){
        $thread = create('App\Thread');
        $this->assertEquals('/threads/'.$thread->channel->slug.'/'.$thread->id, $thread->path());
    }
    /** @test */
    public function aThreadHasACreator(){

        $this->assertInstanceOf('App\User', $this->thread->creator);
    }
    public function aThreadHasReply()
    {

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

    /** @test */
    public function aThreadCanAddAReply(){
        $this->thread->addReply([
            'user_id'   =>1,
            'body'      => 'Foobar',

        ]);
        $this->assertCount(1,$this->thread->replies);
    }
    /** @test */
    public function aThreadHasAChannel(){
        $thread = create('App\Thread');
        $this->assertInstanceOf('App\Channel', $thread->channel);
    }
}
