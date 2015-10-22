<?php

class ExampleTest extends TestCase {

	/**
	 * A basic functional test example.
	 *
	 * @return void
	 */
//	public function testBasicExample()
//	{
//		$response = $this->call('GET', '/');
//
//		$this->assertEquals(200, $response->getStatusCode());
//	}
    public function dummyTest_VariableHasAlwaysValueOfOne()
    {
        $variable = 1;

        //$this->assertEquals(1,$variable );

        $this->assertTrue(1== $variable);

    }

    //proposed test methods

    //testLogin

    //test_Missions_Retrieve

    //test_Missions_Create

    //test_UploadRoute  

}
