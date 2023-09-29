<?php 
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [4,5]);
if ($email) {
    echo '
    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
          <a
            class="nav-item nav-link"
            data-toggle="tab"
            href="#nav-tas"
            role="tab"
            >TAs</a
          >
          <a
            class="nav-item nav-link"
            data-toggle="tab"
            href="#nav-courses"
            role="tab"
            >Courses</a
          >
        </div>
      </nav>
    <div class="tab-content" id="nav-tabContent">
      <br />
      
      <!-- TAs -->
      <div class="tab-pane show active" id="nav-tas" role="tabpanel">
        <div class="container d-flex flex-row">
          <div class="row">
            <div class="col-auto mr-auto">
              <h2 id="title">All TAs</h2>
            </div>
            <div class="col-auto align-self-center">
             
              <!-- Add TAs -->
              <button
                type="button"
                class="btn btn-light"
                id="addModalButton"
                onclick="animateTAModal(\'addTAModal\')"
              >
                <i class="fa fa-plus" style="font-size: 24px"></i>
              </button>
              
              <!-- Modal Start -->
              <div id="addTAModal" class="our-modal">
                <!-- Modal content -->
                <div class="our-modal-add-content">
                  <span id="addTAModal-close" class="our-modal-close">&times;</span>
                  <form id="add-ta-form" class="user-form" action="javascript:saveNewTA()" method="post" autocomplete="off">
                    <h3 style="color: black;"> Add a TA <br /> </h3>
                    <div>
                      <hr></hr>
                      <input class="text-input" id="addTAModal-email" style="width:70%" class="ta-email" type="email" name="ta-email" placeholder="Enter the email of the TA"/>
                    </div>
                    <div>
                      <br />
                      <label for="addTAModal-semester">Semester:</label>
                      <select name="semester" id="addCourseModal-semester">
                        <option value="Fall">Fall</option>
                        <option value="Winter">Winter</option>
                        <option value="Summer">Summer</option>
                      </select>

                      <label for="addTAModal-year">Year:</label>
                      <select name="year" id="addCourseModal-year">
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                      </select>
                    </div>
                    <div>
                      <br />
                      <label for="addTAModal-hours">Maximum Hours:</label>
                      <select name="hours" id="addCourseModal-hours">
                        <option value="90">90</option>
                        <option value="180">180</option>
                      </select>
                    </div>
                    <br />
                    <div id="addTAModal-err">
                    </div>
                    <div>
                      <input class="submit-button" type="submit" value="Submit">
                    </div>
                  </form>
                </div>
              </div>
            </div>
            
            <!-- Add/Remove time to TAs Courses -->
            <div class="col-auto align-self-center">
              <button
                    type="button"
                    class="btn btn-light"
                    id="editModalButton"
                    onclick="animateTAModal(\'editTAModal\')"
                  >
                    <i class="fa fa-pencil fa-fw" style="font-size: 24px"></i>
              </button>

              <!-- Modal Start -->
              <div id="editTAModal" class="our-modal">
                <!-- Modal content -->
                <div class="our-modal-edit-content">
                  <span id="editTAModal-close" class="our-modal-close">&times;</span>
                  <form id="edit-ta-form" class="user-form" action="javascript:displayTA()" method="post" autocomplete="off">
                    <h3 style="color: black;"> Assign/Remove a TA <br /> </h3>
                    <div>
                      <hr></hr>
                      <input class="text-input" id="editTAModal-email" style="width:70%" class="course-email" type="email" name="ta-email" placeholder="Enter the email of the TA"/>
                    </div>
                    <div>
                        <br />
                        <label for="editTAModal-semester">Semester:</label>
                        <select name="semester" id="editTAModal-semester">
                          <option value="Fall">Fall</option>
                          <option value="Winter">Winter</option>
                          <option value="Summer">Summer</option>
                        </select>
                        <label for="editTAModal-year">Year:</label>
                        <select name="year" id="editTAModal-year">
                          <option value="2022">2022</option>
                          <option value="2023">2023</option>
                          <option value="2024">2024</option>
                          <option value="2025">2025</option>
                        </select>
                      </div>
                    <div>
                      <input class="submit-button" type="submit" value="Search">
                    </div>
                  </form>
                    <br />
                    <div id="importantTAinfo"></div>
                    <br />

                    <!-- Second Form -->
                    <div id="assign-removeTA" style = "display:none">
                      <form id="assign-ta-form" class="user-form" action="javascript:assignTA()" method="post" autocomplete="off">
                      <div>
                        <input class="text-input" id="assignTAModal-coursenum" style="width:70%" class="course-num" type="text" name="ta-course" placeholder="Enter the course number."/>
                      </div>
                      <br />
                      <div>
                        <input class="text-input" id="assignTAModal-numhours" style="width:70%" type="text" name="ta-hours" placeholder="Enter number of hours to add/remove."/>
                      </div>
                      <div>
                        <br />
                        <label for="assignTAModal-semester">Semester:</label>
                        <select name="semester" id="assignTAModal-semester">
                          <option value="Fall">Fall</option>
                          <option value="Winter">Winter</option>
                          <option value="Summer">Summer</option>
                        </select>
                        <label for="assignTAModal-year">Year:</label>
                        <select name="year" id="assignTAModal-year">
                          <option value="2022">2022</option>
                          <option value="2023">2023</option>
                          <option value="2024">2024</option>
                          <option value="2025">2025</option>
                        </select>
                      </div>
                      <div>
                            <input type="checkbox" name="assignTA" value="assign"> Add<br>
                            <input type="checkbox" name="removeTA" value="remove"> Remove
                      </div>
                      <br />
                      <div>
                        <input type="submit" value="Submit">
                      </div>
                      </form>
                      <script>
                        document.querySelector(\'input[name="assignTA"]\').addEventListener(\'change\', function() {
                          document.querySelector(\'input[name="removeTA"]\').disabled = this.checked;
                        });
                        document.querySelector(\'input[name="removeTA"]\').addEventListener(\'change\', function() {
                          document.querySelector(\'input[name="assignTA"]\').disabled = this.checked;
                        });
                      </script>
                    </div>
                    <br />
                    <div id="editTAModal-err"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <br />


        <!-- Display TAs -->
        <div id="tas-table"></div>
      </div>

        <!-- Courses -->
      <div class="tab-pane fade" id="nav-courses" role="tabpanel">
        <div class="container d-flex flex-row">
          <div class="row">
            <div class="col-auto mr-auto">
              <h2 id="title">All Courses</h2>
            </div>
          </div>
        </div>
            <br />
            
            <!-- Display Courses -->
        <div id="course-table"></div>
      </div>
    </div>
    <script>
      function loadExistingData() {
        getTACourses();
        getTAs();
      }
      document.onload = loadExistingData();
    </script>
  </body>
</html>';} 
else {
    echo '<div class="welcomeMessage">
    <text><h1><a href="http://localhost/COMP307-Project/ta-management-xampp/xampp-starter/login/login.html">Back to Login</a></h1>.</text>
            </div>';
}
?>