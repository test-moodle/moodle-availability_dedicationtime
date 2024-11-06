@availability @availability_dedicationtime
Feature: availability_dedicationtime
  In order to control student access to activities
  As a teacher
  I need to set dedicationtime conditions which prevent student access

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion | numsections |
      | Course 1 | C1        | topics | 1                | 3           |
    And the following "users" exist:
      | username |
      | teacher1 |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | name  |
      | page     | C1     | P1    |
      | page     | C1     | P2    |
      | page     | C1     | P3    |

  @javascript
  Scenario: Test condition
    # Basic setup.
    Given I am on the "P1" "page activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    Then "Dedication time" "button" should exist in the "Add restriction..." "dialogue"

    # Add a Page P2 with a dedicationtime condition for 5 minutes.
    And I am on the "P2" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Dedication time" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "Dedication time" to "5"
    And I set the field "Dedication time unit" to "minutes"
    And I press "Save and return to course"

    # Add a Page P3 with a dedicationtime condition for 2 hours.
    And I am on the "P3" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Dedication time" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "Dedication time" to "2"
    And I set the field "Dedication time unit" to "hours"
    And I press "Save and return to course"

    # Log back in as student.
    When I am on the "Course 1" "course" page logged in as "student1"

    # No pages should appear yet.
    Then I should not see "P2" in the "region-main" "region"
    And I should not see "P3" in the "region-main" "region"
