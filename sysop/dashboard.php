<?php 
require __DIR__.'/../login/verify.php';
session_start();
$email = verify(session_id(), [5]);
if ($email) {
    echo '
    <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
          <a
            class="nav-item nav-link active"
            data-toggle="tab"
            href="#nav-profs"
            role="tab"
            >Professors</a
          >
          <a
            class="nav-item nav-link"
            data-toggle="tab"
            href="#nav-courses"
            role="tab"
            >Courses</a
          >
          <a
            class="nav-item nav-link"
            data-toggle="tab"
            href="#nav-users"
            role="tab"
            >Users</a
          >
        </div>
      </nav>
      <div class="tab-content" id="nav-tabContent">
        <br />
        <!-- Professors -->
        <div class="tab-pane show active" id="nav-profs" role="tabpanel">
        <div class="container d-flex flex-row">
          <div class="row">
            <div class="col-auto mr-auto">
              <h2 id="title">All Professors</h2>
            </div>
            <div class="col-auto align-self-center">

              <!-- Import Profs -->
              <button
                type="button"
                class="btn btn-outline-secondary"
                id="importProfModalButton"
                onclick="javascript:animateProfModal(\'importProfModal\')"
              >
                <i class="fa fa-download"></i>
                Import
              </button>
              <!-- Modal Start -->
              <div id="importProfModal" class="our-modal">
                <!-- Modal content -->
                <div class="our-modal-import-content">
                  <span id="importProfModal-close" class="our-modal-close">&times;</span>
                  <form id="upload-prof-form" name="upload-prof-form" enctype="multipart/form-data" action="javascript:saveMultipleProfAccounts()" method="post" autocomplete="off" accept=".csv">
                    <h3 style="color: black;"> Import Professors <br /> </h3>
                    <div>
                      <hr></hr>
                      <!-- file upload -->
                      <input id="prof-upload-csv" name="file" type="file" />
                    </div>
                    <br />
                    <div id="importProfModal-err"></div>
                    <div>
                      <input class="submit-button" type="submit" value="Submit">
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Add Profs -->
            <div class="col-auto align-self-center">
              <button
                type="button"
                class="btn btn-light"
                id="addProfModalButton"
                onclick="javascript:animateProfModal(\'addProfModal\')"
              >
                <i class="fa fa-plus" style="font-size: 24px"></i>
              </button>
              <!-- Modal Start -->
              <div id="addProfModal" class="our-modal">
                <!-- Modal content -->
                <div class="our-modal-add-content">
                  <span id="addProfModal-close" class="our-modal-close">&times;</span>
                  <form id="add-prof-form" class="user-form" action="javascript:saveProfAccount()" method="post" autocomplete="off">
                    <h3 style="color: black;"> Add a Professor <br /> </h3>
                    <div>
                      <hr></hr>
                      <input class="text-input" id="addProfModal-email" style="width:70%" class="prof-email" type="email" name="prof-email" placeholder="Enter the email of the instructor"/>
                    </div>
                    <div>
                      <br />
                      <input class="text-input" id="addProfModal-coursenum" style="width:70%" class="course-num" placeholder="Enter the course number" type="text" name="course-num"/>
                    </div>
                    <div>
                      <br />
                      <select id="addProfModal-faculty" name="faculty">
                        <option value="" disabled selected>Select Faculty</option>
                        <option value="Science">Science</option>
                        <option value="Engineering">Engineering</option>
                        <option value="Arts">Arts</option>
                      </select>
                    </div>
                    <div>
                      <br />
                      <select id="addProfModal-department" name="department">
                        <option value="" disabled selected>Select Department</option>
                        <option value="Computer Science">Computer Science</option>
                        <option value="Mathematics">Mathematics</option>
                        <option value="Physics">Physics</option>
                      </select>
                    </div>
                    <br />
                    <div id=addProfModal-err></div>
                    <div>
                      <input class="submit-button" type="submit" value="Submit">
                    </div>
                  </form>
                </div>   
              </div>
            </div>
          </div>
        </div>
            <br />

            <!-- Display Professors -->
        <div id="profs-table"></div>
      </div>

        <!-- Courses -->
      <div class="tab-pane fade" id="nav-courses" role="tabpanel">
        <div class="container d-flex flex-row">
          <div class="row">
            <div class="col-auto mr-auto">
              <h2 id="title">All Courses</h2>
            </div>
            
            <!-- Import Courses -->
            <div class="col-auto align-self-center">
              <button
                type="button"
                class="btn btn-outline-secondary"
                id="importCourseModalButton"
                onclick="animateCourseModal(\'importCourseModal\')"
              >
                <i class="fa fa-download"></i>
                Import
              </button>

              <!-- Modal Start -->
              <div id="importCourseModal" class="our-modal">
                <!-- Modal content -->
                <div class="our-modal-import-content">
                  <span id="importCourseModal-close" class="our-modal-close">&times;</span>
                  <form id="upload-course-form" name="upload-course-form" enctype="multipart/form-data" action="javascript:saveMultipleCourses()" method="post" autocomplete="off" accept=".csv">
                    <h3 style="color: black;"> Import Courses <br /> </h3>
                    <div>
                      <hr></hr>
                      <!-- Import File -->
                      <input id="course-upload-csv" name="file" type="file" />
                    </div>
                    <br />
                    <div id="importCourseModal-err"></div>
                    <div>
                      <input class="submit-button" type="submit" value="Submit">
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Add Course -->
            <div class="col-auto align-self-center">
              <button
                type="button"
                class="btn btn-light"
                id="addCourseModalButton"
                onclick="animateCourseModal(\'addCourseModal\')"
              >
                <i class="fa fa-plus" style="font-size: 24px"></i>
              </button>

              <!-- Modal Start -->
              <div id="addCourseModal" class="our-modal">
                <!-- Modal content -->
                <div class="our-modal-add-content">
                  <span id="addCourseModal-close" class="our-modal-close">&times;</span>
                  <form id="add-course-form" class="user-form" action="javascript:saveCourse()" method="post" autocomplete="off">
                    <h3 style="color: black;"> Add a Course <br /> </h3>
                    <div>
                      <hr></hr>
                      <input class="text-input" id="addCourseModal-coursenum" style="width:70%" class="course-num" placeholder="Enter the course number" type="text" name="course-num"/>
                    </div>
                    <div>
                      <br />
                      <input class="text-input" id="addCourseModal-coursename" style="width:70%" class="course-name" placeholder="Enter the course name" type="text" name="course-name"/>
                    </div>
                    <div>
                      <br />
                      <input class="text-input" id="addCourseModal-desc" style="width:70%" class="course-desc" type="text" name="course-desc" placeholder="Enter the course description"/>
                    </div>
                    <div>
                      <br />
                      <label for="addCourseModal-semester">Semester:</label>
                      <select name="semester" id="addCourseModal-semester">
                        <option value="Fall">Fall</option>
                        <option value="Winter">Winter</option>
                        <option value="Summer">Summer</option>
                      </select>

                      <label for="addCourseModal-year">Year:</label>
                      <select name="year" id="addCourseModal-year">
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                      </select>
                    </div>
                      <br />
                    <div>
                      <input class="text-input" id="addCourseModal-email" style="width:70%" class="course-email" type="email" name="course-email" placeholder="(Optional) Enter the email of the instructor"/>
                    </div>
                    <br />
                    <div id="addCourseModal-err"></div>
                    <div>
                      <input class="submit-button" type="submit" value="Submit">
                    </div>
                  </form>
                </div>
              </div>
            </div>

            <!-- Assign Course to Prof -->
            <div class="col-auto align-self-center">
              <button
                type="button"
                class="btn btn-light"
                id="addCourseModalButton"
                onclick="animateCourseModal(\'assignCourseModal\')"
              >
                <i class="fa fa-pencil fa-fw" style="font-size: 24px"></i>
              </button>

              <!-- Modal Start -->
              <div id="assignCourseModal" class="our-modal">
                <!-- Modal content -->
                <div class="our-modal-add-content">
                  <span id="assignCourseModal-close" class="our-modal-close">&times;</span>
                  <form id="assign-course-form" class="user-form" action="javascript:editCourse()" method="post" autocomplete="off">
                    <h3 style="color: black;"> Assign/Remove a Professor <br /> </h3>
                    <div>
                      <hr></hr>
                      <input class="text-input" id="assignCourseModal-coursenum" style="width:70%" class="course-num" placeholder="Enter the course number" type="text" name="course-num"/>
                    </div>
                    <br />
                    <div>
                      <input class="text-input" id="assignCourseModal-email" style="width:70%" class="course-email" type="email" name="prof-email" placeholder="Enter the email of the instructor"/>
                    </div>
                    <div>
                      <br />
                      <label for="assignCourseModal-semester">Semester:</label>
                      <select name="semester" id="addCourseModal-semester">
                        <option value="Fall">Fall</option>
                        <option value="Winter">Winter</option>
                        <option value="Summer">Summer</option>
                      </select>
                      <label for="assignCourseModal-year">Year:</label>
                      <select name="year" id="addCourseModal-year">
                        <option value="2022">2022</option>
                        <option value="2023">2023</option>
                        <option value="2024">2024</option>
                        <option value="2025">2025</option>
                      </select>
                    </div>
                    <br />
                    <div>
                          <input type="checkbox" name="assign" value="assign"> Assign<br>
                          <input type="checkbox" name="remove" value="remove"> Remove
                    </div>
                    <br />
                    <div id="assignCourseModal-err"></div>
                    <div>
                      <input class="submit-button" type="submit" value="Submit">
                    </div>
                  </form>

                  <!-- Small Script for Checkbox -->
                  <script>
                    document.querySelector(\'input[name="assign"]\').addEventListener(\'change\', function() {
                      document.querySelector(\'input[name="remove"]\').disabled = this.checked;
                    });
                    document.querySelector(\'input[name="remove"]\').addEventListener(\'change\', function() {
                      document.querySelector(\'input[name="assign"]\').disabled = this.checked;
                    });
                  </script>
                </div>
              </div>
            </div>
          </div>
        </div>
            <br />

        <!-- Display Courses -->
        <div id="course-table"></div>
      </div>

        <!-- Users -->
        <div class="tab-pane fade" id="nav-users" role="tabpanel">
          <div class="container d-flex flex-row">
            <div class="row">
              <div class="col-auto mr-auto">
                <h2 id="title">All Users</h2>
              </div>

              <!-- Import Users -->
              <div class="col-auto align-self-center">
                <button
                  type="button"
                  class="btn btn-outline-secondary"
                  id="importModalButton"
                  onclick="animateModal(\'importModal\')"
                >
                <i class="fa fa-download"></i>
                Import
                </button>

                <!-- Modal Start -->
                <div id="importModal" class="our-modal">
                  <!-- Modal content -->
                  <div class="our-modal-import-content">
                    <span id="importModal-close" class="our-modal-close">&times;</span>
                    <form id="upload-user-form" name="upload-user-form" enctype="multipart/form-data" action="javascript:saveMultipleNewAccounts()" method="post" autocomplete="off" accept=".csv">
                      <h3 style="color: black;"> Import Users <br /> </h3>
                      <div>
                        <hr></hr>
                        <!-- Upload File -->
                        <input id="user-upload-csv" name="file" type="file" />
                      </div>
                      <br />
                      <div id="importModal-err"></div>
                      <div>
                        <input class="submit-button" type="submit" value="Submit">
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <div class="col-auto align-self-center">

                <!-- Add Users -->
                <button
                  type="button"
                  class="btn btn-light"
                  id="addModalButton"
                  onclick="animateModal(\'addModal\')"
                >
                  <i class="fa fa-plus" style="font-size: 24px"></i>
                </button>

                <!-- Modal Start -->
                <div id="addModal" class="our-modal">
                  <!-- Modal content -->
                  <div class="our-modal-add-content">
                    <span id="addModal-close" class="our-modal-close">&times;</span>
                    <form id="add-user-form" class="user-form" action="javascript:saveNewAccount()" method="post" autocomplete="off">
                      <h3 style="color: black;"> Add a User <br /> </h3>
                      <div>
                        <hr></hr>
                        <input class="text-input" id="addModal-fname" style="width:70%" class="user-fname" placeholder="Enter the first name of the user" type="text" name="first-name"/>
                      </div>
                      <div>
                      <br />
                      <input class="text-input" id="addModal-lname" style="width:70%" class="user-lname" placeholder="Enter the last name of the user" type="text" name="last-name"/>
                      </div>
                      <div>
                      <br />
                      <input class="text-input" id="addModal-email" style="width:70%" class="user-email" type="email" name="email" placeholder="Enter the email of the user."/>
                      </div>
                      <div>
                      <br />
                      <input class="text-input" id="addModal-password" style="width:70%" class="user-password" placeholder="Enter password" type="password" name="password" />
                      </div>
                      <div id="studentID-div" style="display: none;">
                      <br />
                      <input class="text-input" id="addModal-studentid" style="width:70%" class="user-stdid" placeholder="Enter studentID" type="text" name="sid" />
                      </div>
                      <br />
                      <div>
                        <div>
                          <input type="checkbox" name="student" value="student"> Student<br>
                          <input type="checkbox" name="professor" value="professor"> Professor
                        </div>
                        <div>
                          <input type="checkbox" name="admin" value="admin"> TA Administrator<br>
                          <input type="checkbox" name="ta" value="ta"> Teaching Assistant
                        </div>
                        <div>
                          <input type="checkbox" name="sysop" value="sysop"> System Operator
                        </div>
                      </div>
                      <div id="addModal-err">
                      </div>
                      <div>
                        <input class="submit-button" type="submit" value="Submit">
                      </div>              
                    </form> 
                  </div>

                  <!-- Same Script again -->
                  <script>
                      document.querySelector(\'input[name="student"]\').addEventListener(\'change\', function() {
                        document.getElementById(\'studentID-div\').style.display = this.checked ? \'block\' : \'none\';
                      });
                      
                      document.querySelector(\'input[name="ta"]\').addEventListener(\'change\', function() {
                        document.getElementById(\'studentID-div\').style.display = this.checked ? \'block\' : \'none\';
                      });    
                  </script>   
                </div>
              </div>


              <!-- Remove Users -->
              <div class="col-auto align-self-center">
                <button
                      type="button"
                      class="btn btn-light"
                      id="removeModalButton"
                      onclick="animateModal(\'removeModal\')"
                    >
                      <i class="fa fa-minus" style="font-size: 24px"></i>
                </button>

                <!-- Modal Start -->
                <div id="removeModal" class="our-modal">
                  <!-- Modal content -->
                  <div class="our-modal-remove-content">
                    <span id="removeModal-close" class="our-modal-close">&times;</span>
                    <form id="remove-user-form" class="user-form" action="javascript:removeAccount()" method="post" autocomplete="off">
                      <h3 style="color: black;"> Remove a User <br /> </h3>
                      <div>
                        <hr></hr>
                        <input id="removeModal-email" class="user-email" style="width:70%" type="email" name="email" placeholder="Enter the email of the user.">
                      </div>
                      <div id="removeModal-err">
                      </div>
                      <div>
                        <input class="submit-button" type="submit" value="Submit">
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Edit Users -->
              <div class="col-auto align-self-center">
                <button
                      type="button"
                      class="btn btn-light"
                      id="editModalButton"
                      onclick="animateModal(\'editModal\')"
                    >
                      <i class="fa fa-pencil fa-fw" style="font-size: 24px"></i>
                </button>

                <!-- Modal Start -->
                <div id="editModal" class="our-modal">
                  <!-- Modal content -->
                  <div class="our-modal-edit-content">
                    <span id="editModal-close" class="our-modal-close">&times;</span>
                    <form id="edit-user-form" class="user-form" action="javascript:editAccount()" method="post" autocomplete="off">
                      <h3 style="color: black;"> Edit a User <br /> </h3>
                      <div>
                        <hr></hr>
                        <input id="editModal-email"  style="width:70%" type="email" name="email" placeholder="Enter the email of the user.">
                      </div>
                      <br />
                      <div id="editModal-table"></div>
                      <div id="editModal-err"></div>
                      <div>
                        <input class="submit-button" type="submit" value="Search">
                      </div>
                    </form>
                    <form id="emailForm" action="javascript:submitEmailChange()" method="post" autocomplete="off" hidden></form>
                    <form id="fnameForm" action="javascript:submitFnameChange()" method="post" autocomplete="off" hidden></form>
                    <form id="lnameForm" action="javascript:submitLnameChange()" method="post" autocomplete="off" hidden></form>
                  </div>

                  <!-- Second Modal for User Type Edit -->
                  <div id="usertypeModal" class="our-modal">
                    <div class="our-modal-usertype-content">
                    <span id="usertypeModal-close" class="our-modal-close">&times;</span>
                    <h3 style="color: black;"> Select User Type(s) <br /> </h3>
                      <br />
                      <div>
                        <form id="usertypeForm" class="user-form" action="javascript:editUserTypes()" method="post" autocomplete="off">
                          <div>
                          <input
                            type="checkbox"
                            name="usertype[]"
                            value="1"
                            id="student"
                          />
                          <label
                            for="student"
                            >Student</label
                          >
                        </div>
                        <div>
                          <input
                            type="checkbox"
                            name="usertype[]"
                            value="2"
                            id="professor"
                          />
                          <label
                            for="professor"
                            >Professor</label
                          >
                        </div>
                        <div>
                          <input
                            type="checkbox"
                            name="usertype[]"
                            value="4"
                            id="admin"
                          />
                          <label 
                            for="admin"
                            >TA Administrator</label
                          >
                        </div>
                        <div>
                          <input
                            type="checkbox"
                            name="usertype[]"
                            value="3"
                            id="ta"
                          />
                          <label 
                            for="ta"
                            >Teaching Assistant</label
                          >
                        </div>
                        <div>
                          <input
                            type="checkbox"
                            name="usertype[]"
                            value="5"
                            id="sysop"
                          />
                          <label 
                            for="sysop"
                            >System Operator</label
                          >
                        </div>
                        <div id="usertypeModal-err"></div>
                        <input class="submit-button" type="submit" value="Submit">
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <br />
          
          <!-- Display Users -->
          <div id="user-table"></div>
        </div>
      </div>
    </div>
    <script>
      function loadExistingData() {
        getProfAccounts();
        getCourses();
        getAccounts();
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