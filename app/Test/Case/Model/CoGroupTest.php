<?php
/**
 * COmanage Registry CO Group Test
 *
 * Portions licensed to the University Corporation for Advanced Internet
 * Development, Inc. ("UCAID") under one or more contributor license agreements.
 * See the NOTICE file distributed with this work for additional information
 * regarding copyright ownership.
 *
 * UCAID licenses this file to you under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with the
 * License. You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @link          http://www.internet2.edu/comanage COmanage Project
 * @package       registry
 * @since         COmanage Registry vX.Y.Z
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 */

App::uses('CoGroup', 'Model');

class CoGroupTest extends CakeTestCase {

  public $fixtures = array(
    'app.Co',
    'app.Cou',
    'app.CoEnrollmentFlow',
    'app.CoExpirationPolicy',
    'app.CoGroup',
    'app.CoGroupMember',
    'app.CoNotification',
    'app.CoProvisioningExport',
    'app.CoProvisioningTarget',
    'app.CoService',
    'app.CoSetting',
    'app.HistoryRecord'
  );

  /**
   * Set up the test case.
   */
  public function setUp() {
    parent::setUp();
    $this->CoGroup = ClassRegistry::init('CoGroup');
  }

  /**
   * Tear down the test case.
   */
  public function tearDown() {
    unset($this->CoGroup);
    parent::tearDown();
  }

  /**
   * Test adding default groups to a Co that does not exist.
   */
  public function testAddDefaultsNoCoId() {
    $this->setExpectedException(InvalidArgumentException::class, 'CO "CO_ID_THAT_DOES_NOT_EXIST" Not Found');
    $this->CoGroup->addDefaults('CO_ID_THAT_DOES_NOT_EXIST');
  }

  /**
   * Test adding default groups to a Cou that does not exist.
   */
  public function testAddDefaultsNoCouId() {
    $this->setExpectedException(InvalidArgumentException::class, 'COU "COU_ID_THAT_DOES_NOT_EXIST" Not Found');
    $this->CoGroup->addDefaults(1, 'COU_ID_THAT_DOES_NOT_EXIST');
  }

  /**
   * Test adding default groups to a Co.
   */
  public function testAddDefaultsToCo() {

    // Get a Co object.
    $Co = ClassRegistry::init('Co');

    // Find Co with id 2 from the fixture.
    $args = array();
    $args['conditions']['Co.id'] = '2';
    $args['contain'] = false;
    $result = $Co->find('first', $args);
    $this->assertNotNull($result);
    $this->assertNotEmpty($result);

    // Find all groups with Co id 2, should not find any.
    $args = array();
    $args['conditions']['CoGroup.co_id'] = '2';
    $args['conditions']['CoGroup.deleted'] = false;
    $args['contain'] = false;
    $result = $this->CoGroup->find('all', $args);
    $expected = array();
    $this->assertEquals($expected, $result);

    // Call setup() on the Cou with id 1 and Co with id 2 from the fixture.
    $return = $this->CoGroup->addDefaults(2);
    // TODO $this->assertTrue($return);

    // Find all groups with Cou id 1, should find the default groups.
    $result = $this->CoGroup->find('all', $args);
    $this->assertNotNull($result);
    $this->assertNotEmpty($result);

    // Ignore 'created' and 'modified' timestamps.
    $result = Hash::remove($result, '{n}.CoGroup.created');
    $result = Hash::remove($result, '{n}.CoGroup.modified');

    $expected = array(
      array(
        'CoGroup' => array(
          'id'               => '1',
          'co_id'            => '2',
          'cou_id'           => NULL,
          'name'             => 'CO:admins',
          'description'      => 'Test CO 1 Administrators',
          'open'             => false,
          'status'           => 'A',
          'group_type'       => 'A',
          'auto'             => false,
          'co_group_id'      => NULL,
          'revision'         => '0',
          'deleted'          => false,
          'actor_identifier' => NULL,
        )),
      array(
        'CoGroup' => array(
          'id'               => '2',
          'co_id'            => '2',
          'cou_id'           => NULL,
          'name'             => 'CO:members:active',
          'description'      => 'Test CO 1 Active Members',
          'open'             => false,
          'status'           => 'A',
          'group_type'       => 'MA',
          'auto'             => true,
          'co_group_id'      => NULL,
          'revision'         => '0',
          'deleted'          => false,
          'actor_identifier' => NULL,
        )),
      array(
        'CoGroup' => array(
          'id'               => '3',
          'co_id'            => '2',
          'cou_id'           => NULL,
          'name'             => 'CO:members:all',
          'description'      => 'Test CO 1 Members',
          'open'             => false,
          'status'           => 'A',
          'group_type'       => 'M',
          'auto'             => true,
          'co_group_id'      => NULL,
          'revision'         => '0',
          'deleted'          => false,
          'actor_identifier' => NULL,
        )),
    );

    $this->assertEquals($expected, $result);
  }

  /**
   * Test adding default groups to a Cou.
   */
  public function testAddDefaultsToCou() {

    // Get a Cou object.
    $Cou = ClassRegistry::init('Cou');

    // Find Cou with id 1 from the fixture.
    $args = array();
    $args['conditions']['Cou.id'] = '1';
    $args['contain'] = false;
    $result = $Cou->find('first', $args);
    $this->assertNotNull($result);
    $this->assertNotEmpty($result);

    // Find all groups with Cou id 1, should not find any.
    $args = array();
    $args['conditions']['CoGroup.cou_id'] = '1';
    $args['conditions']['CoGroup.deleted'] = false;
    $args['contain'] = false;
    $result = $this->CoGroup->find('all', $args);
    $expected = array();
    $this->assertEquals($expected, $result);

    // Call setup() on the Cou with id 1 and Co with id 2 from the fixture.
    $return = $this->CoGroup->addDefaults(2, 1);
    // TODO $this->assertTrue($return);

    // Find all groups with Cou id 1, should find the default groups.
    $result = $this->CoGroup->find('all', $args);
    $this->assertNotNull($result);
    $this->assertNotEmpty($result);

    // Ignore 'created' and 'modified' timestamps.
    $result = Hash::remove($result, '{n}.CoGroup.created');
    $result = Hash::remove($result, '{n}.CoGroup.modified');

    $expected = array(
      array(
        'CoGroup' => array(
          'id'               => '1',
          'co_id'            => '2',
          'cou_id'           => '1',
          'name'             => 'CO:COU:Test COU 1:admins',
          'description'      => 'Test COU 1 Administrators',
          'open'             => false,
          'status'           => 'A',
          'group_type'       => 'A',
          'auto'             => false,
          'co_group_id'      => NULL,
          'revision'         => '0',
          'deleted'          => false,
          'actor_identifier' => NULL,
        )),
      array(
        'CoGroup' => array(
          'id'               => '2',
          'co_id'            => '2',
          'cou_id'           => '1',
          'name'             => 'CO:COU:Test COU 1:members:active',
          'description'      => 'Test COU 1 Active Members',
          'open'             => false,
          'status'           => 'A',
          'group_type'       => 'MA',
          'auto'             => true,
          'co_group_id'      => NULL,
          'revision'         => '0',
          'deleted'          => false,
          'actor_identifier' => NULL,
        )),
      array(
        'CoGroup' => array(
          'id'               => '3',
          'co_id'            => '2',
          'cou_id'           => '1',
          'name'             => 'CO:COU:Test COU 1:members:all',
          'description'      => 'Test COU 1 Members',
          'open'             => false,
          'status'           => 'A',
          'group_type'       => 'M',
          'auto'             => true,
          'co_group_id'      => NULL,
          'revision'         => '0',
          'deleted'          => false,
          'actor_identifier' => NULL,
        )),
    );

    $this->assertEquals($expected, $result);
  }

  /**
   * Test obtaining the ID of the CO admin group which does not exist.
   */
  public function testAdminCoGroupIdCoNotFound() {
    $this->setExpectedException(InvalidArgumentException::class, 'Group admins Not Found');
    $this->CoGroup->adminCoGroupId(2);
  }

  /**
   * Test obtaining the ID of the admin group for CO with ID 1.
   */
  public function testAdminCoGroupIdCo() {
    $expected = 1;
    $this->CoGroup->addDefaults(1);
    $actual = $this->CoGroup->adminCoGroupId(1);
    $this->assertEquals($expected, $actual);
  }

  /**
   * Test obtaining the ID of the admin group for CO with ID 2.
   */
  public function testAdminCoGroupIdCos() {
    $expected = 4;
    $this->CoGroup->addDefaults(1);
    $this->CoGroup->addDefaults(2);
    $actual = $this->CoGroup->adminCoGroupId(2);
    $this->assertEquals($expected, $actual);
  }

  /**
   * Test obtaining the ID of the COU admin group.
   */
  public function testAdminCoGroupIdCou() {
    $expected = 7;
    $this->CoGroup->addDefaults(1);
    $this->CoGroup->addDefaults(2);
    $this->CoGroup->addDefaults(2, 1);
    $actual = $this->CoGroup->adminCoGroupId(2, 1);

    $this->assertEquals($expected, $actual);
  }

  /**
   * testFindForCoPerson method
   *
   * @return void
   */
  public function testFindForCoPerson() {
    $this->markTestIncomplete('testFindForCoPerson not implemented.');
  }

  /**
   * testFindSortedMembers method
   *
   * @return void
   */
  public function testFindSortedMembers() {
    $this->markTestIncomplete('testFindSortedMembers not implemented.');
  }

  /**
   * testIsCouAdminGroup method
   *
   * @return void
   */
  public function testIsCouAdminGroup() {
    $this->markTestIncomplete('testIsCouAdminGroup not implemented.');
  }

  /**
   * testIsCouAdminOrMembersGroup method
   *
   * @return void
   */
  public function testIsCouAdminOrMembersGroup() {
    $this->markTestIncomplete('testIsCouAdminOrMembersGroup not implemented.');
  }

  /**
   * testIsCoMembersGroup method
   *
   * @return void
   */
  public function testIsCoMembersGroup() {
    $this->markTestIncomplete('testIsCoMembersGroup not implemented.');
  }

  /**
   * testIsCouMembersGroup method
   *
   * @return void
   */
  public function testIsCouMembersGroup() {
    $this->markTestIncomplete('testIsCouMembersGroup not implemented.');
  }

  /**
   * testProvisioningStatus method
   *
   * @return void
   */
  public function testProvisioningStatus() {
    $this->markTestIncomplete('testProvisioningStatus not implemented.');
  }

  /**
   * testReadOnly method
   *
   * @return void
   */
  public function testReadOnly() {
    $this->markTestIncomplete('testReadOnly not implemented.');
  }

  /**
   * testReconcileAutomaticGroup method
   *
   * @return void
   */
  public function testReconcileAutomaticGroup() {
    $this->markTestIncomplete('testReconcileAutomaticGroup not implemented.');
  }

  /**
   * Test creating a group.
   */
  public function testCreateCoGroup() {

    // Find all groups with CO id 2, should not find any.
    $args = array();
    $args['conditions']['CoGroup.co_id'] = '2';
    $args['conditions']['CoGroup.deleted'] = false;
    $args['contain'] = false;
    $result = $this->CoGroup->find('all', $args);
    $expected = array();
    $this->assertEquals($expected, $result);

    // Create the group
    $group = array();
    $group['auto'] = false;
    $group['co_id'] = 2;
    $group['description'] = 'Test Create Group';
    $group['group_type'] = GroupEnum::Standard;
    $group['name'] = 'testCreateGroup';
    $group['open'] = false;
    $group['status'] = SuspendableStatusEnum::Active;

    // Test result of save operation.
    $result = $this->CoGroup->save($group);
    $this->assertNotNull($result);
    $this->assertNotEmpty($result);
    $result = Hash::remove($result, 'CoGroup.created'); // Ignore 'created' timestamp
    $result = Hash::remove($result, 'CoGroup.modified'); // Ignore 'modified' timestamp
    $expected = array(
      'CoGroup' => array(
        'id'               => '1',
        'co_id'            => '2',
        'name'             => 'testCreateGroup',
        'description'      => 'Test Create Group',
        'open'             => false,
        'status'           => 'A',
        'group_type'       => 'S',
        'auto'             => false,
        'co_group_id'      => NULL,
        'revision'         => '0',
        'deleted'          => false,
        'actor_identifier' => NULL,
      ),
    );
    $this->assertEquals($expected, $result);

    // Find all groups with Co id 2, should find one.
    $result = $this->CoGroup->find('all', $args);

    // Test result of find operation.
    $result = Hash::remove($result, '{n}.CoGroup.created'); // Ignore 'created' timestamp
    $result = Hash::remove($result, '{n}.CoGroup.modified'); // Ignore 'modified' timestamp
    $expected = array($expected);
    $expected['0']['CoGroup']['cou_id'] = NULL; // Add COU ID to expected
    $this->assertEquals($expected, $result);
  }


  /**
   * Test creating a group.
   */
  public function testCreateCouGroup() {

    // Find all groups with CO id 2, should not find any.
    $args = array();
    $args['conditions']['CoGroup.co_id'] = '2';
    $args['conditions']['CoGroup.deleted'] = false;
    $args['contain'] = false;
    $result = $this->CoGroup->find('all', $args);
    $expected = array();
    $this->assertEquals($expected, $result);

    // Create the group
    $group = array();
    $group['auto'] = false;
    $group['co_id'] = 2;
    $group['cou_id'] = 1;
    $group['description'] = 'Test Create Group';
    $group['group_type'] = GroupEnum::Standard;
    $group['name'] = 'testCreateGroup';
    $group['open'] = false;
    $group['status'] = SuspendableStatusEnum::Active;

    // Test result of save operation.
    $result = $this->CoGroup->save($group);
    $this->assertNotNull($result);
    $this->assertNotEmpty($result);
    $result = Hash::remove($result, 'CoGroup.created'); // Ignore 'created' timestamp
    $result = Hash::remove($result, 'CoGroup.modified'); // Ignore 'modified' timestamp
    $expected = array(
      'CoGroup' => array(
        'id'               => '1',
        'co_id'            => '2',
        'cou_id'           => '1',
        'name'             => 'testCreateGroup',
        'description'      => 'Test Create Group',
        'open'             => false,
        'status'           => 'A',
        'group_type'       => 'S',
        'auto'             => false,
        'co_group_id'      => NULL,
        'revision'         => '0',
        'deleted'          => false,
        'actor_identifier' => NULL,
      ),
    );
    $this->assertEquals($expected, $result);

    // Find all groups with Co id 2, should find one.
    $result = $this->CoGroup->find('all', $args);

    // Test result of find operation.
    $result = Hash::remove($result, '{n}.CoGroup.created'); // Ignore 'created' timestamp
    $result = Hash::remove($result, '{n}.CoGroup.modified'); // Ignore 'modified' timestamp
    $expected = array($expected);
    $this->assertEquals($expected, $result);
  }

  /**
   * Test modifying a group.
   */
  public function testModifyGroup() {

    // Create a group.
    $this->testCreateCouGroup();

    // Find the newly created group.
    $args = array();
    $args['conditions']['CoGroup.name'] = 'testCreateGroup';
    $args['conditions']['CoGroup.co_id'] = '2';
    $args['conditions']['CoGroup.cou_id'] = 1;
    $args['conditions']['CoGroup.deleted'] = false;
    $args['contain'] = false;
    $result = $this->CoGroup->find('first', $args);
    $this->assertNotNull($result);
    $this->assertNotEmpty($result);
    $result = Hash::remove($result, 'CoGroup.created'); // Ignore 'created' timestamp
    $result = Hash::remove($result, 'CoGroup.modified'); // Ignore 'modified' timestamp
    $expected = array(
      'CoGroup' => array(
        'id'               => '1',
        'co_id'            => '2',
        'cou_id'           => '1',
        'name'             => 'testCreateGroup',
        'description'      => 'Test Create Group',
        'open'             => false,
        'status'           => 'A',
        'group_type'       => 'S',
        'auto'             => false,
        'co_group_id'      => NULL,
        'revision'         => '0',
        'deleted'          => false,
        'actor_identifier' => NULL,
      ),
    );
    $this->assertEquals($expected, $result);

    // Modify the group.
    $result['0']['CoGroup']['description'] = 'New Description';

    if (!$this->CoGroup->save($result)) {
      $this->fail("Test failed with errors :\n".var_export($this->CoGroup->validationErrors, true));
    }

    // Find group after modification and assert modification
    $result = $this->CoGroup->find('first', $args);
    $result = Hash::remove($result, 'CoGroup.created'); // Ignore 'created' timestamp
    $result = Hash::remove($result, 'CoGroup.modified'); // Ignore 'modified' timestamp
    $expected['CoGroup']['revision'] = '1';
    $expected['CoGroup']['description'] = 'New Description';
    $this->assertEquals($expected, $result);
  }

}
