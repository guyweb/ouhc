<?php
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/oudocs/OUDocs.php' );
$OUDocs = new \OUFabric\OUDocs( 'oucommon/models', 'v1' );
$OUDocs->printHead();
?>
   
   <div class="content">
      <h1>Models</h1>
      
      <p>OU Common exposes programmatic structures (models) that can be used to fetch information about a particular business construct. These models are outlined below.</p>
      
      <!-- Page nav -->
      <ul class="pagenav">
      	 <li><a href="#using-models">Using OU Common Models</a></li>
         <li><a href="#model-qualification">The Qualification Model</a></li>
         <li><a href="#model-module">The Module Model</a></li>
         <li><a href="#model-subject">The Subject Model</a></li>
         <li><a href="#model-user">The User Model</a></li>
         <li><a href="#model-student">The Student Model</a></li>
         <li><a href="#model-tutor">The Tutor Model</a></li>
         <li><a href="#model-staff">The Staff Model</a></li>
         <li><a href="#model-visitor">The Visitor Model</a></li>
         <li><a href="#model-tutorgroup">The TutorGroup Model</a></li>
      </ul>
      
      <h2 class="section" id="using-models">Using OU Common Models</h2>
      <p>OU Common Models are available once the OU Common library is loaded. Once loaded, they can be accessed within the <code>OUFabric\OUCommon\Models</code> PHP namespace.</p>
      <h3>Code example</h3>
      <pre>&lt;?php
// Load the OU Common PHP library
require_once( $_SERVER['DOCUMENT_ROOT'] . '/ldt_shared/oufabric/oucommon/v1/OUCommon.php' );

// Get a qualification
$Qualification = new \OUFabric\OUCommon\Models\Qualification( 'Q69' );

// Print the object
print_r( $Qualification );

// EOF</pre>
      
      
      
      
      <h2 class="section" id="model-qualification">The Qualification Model</h2>
      <p>The Qualification model represents an OU qualification.</p>
      
      <h3>How to instantiate</h3>
      <p>The Qualification model expects one parameter to be passed in to the constructor, which is the qualification code. e.g.:</p>
      <pre>// Get a qualification
$qualification = new \OUFabric\OUCommon\Models\Qualification( 'Q69' );</pre>
      
      <h3>Example object</h3>
      <pre>OUFabric\OUCommon\Models\Qualification Object
(
    [level] => undergraduate
    [framework] => Q1
    [title] => BA  (Hons) / BSc (Hons) Combined Social Sciences
    [code] => Q69
    [withdrawalDate] => 2017-12-31
    [subjects] => Array
        (
            [0] => OUFabric\OUCommon\Models\Subject Object
                (
                    [id] => 14
                    [title] => Social Sciences
                    [slug] => socialscience
                )

            [1] => OUFabric\OUCommon\Models\Subject Object
                (
                    [id] => 12
                    [title] => Psychology
                    [slug] => psychology
                )

        )

    [id:OUFabric\OUCommon\Models\Qualification:private] => 118
)</pre>
		
        <h3>Object breakdown</h3>
      	<ul>
        	<li>(string) <strong>level</strong>: Represents the level of the qualification. Possible values are:
            	<ul>
                	<li><strong>undergraduate</strong> (const: \OUFabric\OUCommon\Models\Qualification::LEVEL_UNDERGRADUATE)</li>
                    <li><strong>postgraduate</strong> (const: \OUFabric\OUCommon\Models\Qualification::LEVEL_POSTGRADUATE)</li>
                </ul>
            </li>
            <li>(string) <strong>framework</strong>: Represents the framework type that the qualification exists within. Possible values are:
            	<ul>
                	<li><strong>M</strong> (const: \OUFabric\OUCommon\Models\Qualification::FRAMEWORK_TYPE_M)</li>
                    <li><strong>Q0</strong> (const: \OUFabric\OUCommon\Models\Qualification::FRAMEWORK_TYPE_Q0)</li>
                    <li><strong>Q1</strong> (const: \OUFabric\OUCommon\Models\Qualification::FRAMEWORK_TYPE_Q1)</li>
                    <li><strong>Q2</strong> (const: \OUFabric\OUCommon\Models\Qualification::FRAMEWORK_TYPE_Q2)</li>
                </ul>
            </li>
            <li>(string) <strong>title</strong>: The title of the qualification</li>
            <li>(string) <strong>code</strong>: The qualification code</li>
            <li>(string) <strong>withdrawalDate</strong>: The qualification withdrawal date</li>
            <li>(array) <strong>subjects</strong>: An array of <a href="#model-subject">Subject models</a> relevant to the qualification</li>
        </ul>
      
      
      
      
      <h2 class="section" id="model-module">The Module Model</h2>
      <p>The Module model represents an OU module.</p>
      
      <h3>How to instantiate</h3>
      <p>The Module model expects one parameter to be passed in to the constructor, which is the module code. e.g.:</p>
      <pre>// Get a module
$module = new \OUFabric\OUCommon\Models\Module( 'M811' );</pre>
      
      <h3>Example object</h3>
      <pre>OUFabric\OUCommon\Models\Module Object
(
    [name] => Information security
    [code] => M811
    [level] => postgraduate
)</pre>
      
      <h3>Object breakdown</h3>
      <ul>
      	<li>(string) <strong>name</strong>: The name of the module</li>
        <li>(string) <strong>code</strong>: The module code</li>
        <li>(string) <strong>level</strong>: Represents the level of the module. Possible values are:
        	<ul>
            	<li><strong>undergraduate</strong> (const: \OUFabric\OUCommon\Models\Module::LEVEL_UNDERGRADUATE)</li>
                <li><strong>postgraduate</strong> (const: \OUFabric\OUCommon\Models\Module::LEVEL_POSTGRADUATE)</li>
            </ul>
        </li>
      </ul>
      
      
      
      
      
      <h2 class="section" id="model-subject">The Subject Model</h2>
      <p>The Subject model represents an OU subject.</p>
      
      <h3>How to instantiate</h3>
      <p>The Subject model expects one parameter to be passed in to the constructor, which is the subject code (integer). e.g.:</p>
      <pre>// Get a subject
$module = new \OUFabric\OUCommon\Models\Subject( 12 );</pre>
      
      <h3>Example object</h3>
      <pre>OUFabric\OUCommon\Models\Subject Object
(
    [id] => 12
    [title] => Psychology
    [slug] => psychology
)</pre>
      
      <h3>Object breakdown</h3>
      <ul>
      	<li>(int) <strong>id</strong>: The ID of the subject</li>
        <li>(string) <strong>title</strong>: The subject title</li>
        <li>(string) <strong>slug</strong>: A <a href="https://en.wikipedia.org/wiki/Clean_URL#Slug" target="_blank">slug</a> that can be used in URLs</li>
      </ul>
      
      
      
      
      <h2 class="section" id="model-user">The User Model</h2>
      <p>The User model represents an individual at the OU.</p>
      
      <h3>How to instantiate</h3>
      <p>The user models cannot be instantiated as there is currently no data source available to retrieve user information on an individual basis. However, this model is made use of by OU Graph, which uses data about the current viewing user to populate the model.</p>
      
      <h3>Inheritance</h3>
      <p>The User model is used as a base model (e.g. it is a set of common traits) upon which all other user models (Student, Tutor, Visitor, Staff) build themselves around. In other words, the properties seen below are common to all user models.</p>
      
      <h3>Example object</h3>
      <pre>OUFabric\OUCommon\Models\User Object
(
    [type] => visitor
    [id] => Q0507983
    [oucu] => jc27976
    [displayName] => Jack Chapple
)</pre>
      
      <h3>Object breakdown</h3>
      <ul>
      	<li>(string) <strong>type</strong>: The user type. Possible values are:
        	<ul>
            	<li><strong>staff</strong> (const: \OUFabric\OUCommon\Models\User::TYPE_STAFF)</li>
                <li><strong>student</strong> (const: \OUFabric\OUCommon\Models\User::TYPE_STUDENT)</li>
                <li><strong>tutor</strong> (const: \OUFabric\OUCommon\Models\User::TYPE_TUTOR)</li>
                <li><strong>visitor</strong> (const: \OUFabric\OUCommon\Models\User::TYPE_VISITOR)</li>
            </ul>
        </li>
        <li>(string) <strong>id</strong>: The user's ID</li>
        <li>(string) <strong>oucu</strong>: The student's OUCU</li>
        <li>(string) <strong>displayName</strong>: The student's display name</li>
      </ul>
      
      <h3>Object methods</h3>
      <ul>
      	<li><strong>$user->getFirstName()</strong>: Self-explanatory!</li>
      </ul>
      
      
      
      <h2 class="section" id="model-student">The Student Model</h2>
      <p>The Student model represents a student at the OU.</p>
      
      <h3>Inheritance</h3>
      <p>The student model inherits common traits from the <a href="#model-user">User model</a>.</p>
      
      <h3>Example object</h3>
      <pre>OUFabric\OUCommon\Models\Student Object
(
    [type] => student
    [id] => Q0507983
    [oucu] => jc27976
    [displayName] => Jack Chapple
    [status] => registered
    [level] => undergraduate
    [framework] => Q1
    [regionCode] =>
    [pricingCode] => EN
    [qualifications] => Array
        (
            [...]
        )
        
    [modules] => Array
        (
            [...]
        )
        
)</pre>

		<h3>Object breakdown</h3>
        <ul>
        	<li>(string) <strong>status</strong>: The 'status' of the student. Possible values are:
            	<ul>
                	<li><strong>access</strong> (const: \OUFabric\OUCommon\Models\Student::STATUS_TYPE_ACCESS)</li>
                    <li><strong>openings</strong> (const: \OUFabric\OUCommon\Models\Student::STATUS_TYPE_OPENINGS)</li>
                    <li><strong>reserved</strong> (const: \OUFabric\OUCommon\Models\Student::STATUS_TYPE_RESERVED)</li>
                    <li><strong>reserved_mod</strong> (const: \OUFabric\OUCommon\Models\Student::STATUS_TYPE_RESERVED_MOD)</li>
                    <li><strong>registered</strong> (const: \OUFabric\OUCommon\Models\Student::STATUS_TYPE_REGISTERED)</li>
                </ul>
            </li>
            <li>(string) <strong>level</strong>: The level of the student. Possible values are:
            	<ul>
                	<li><strong>undergraduate</strong> (const: \OUFabric\OUCommon\Models\Qualification::LEVEL_UNDERGRADUATE)</li>
                    <li><strong>postgraduate</strong> (const: \OUFabric\OUCommon\Models\Qualification::LEVEL_POSTGRADUATE)</li>
                </ul>
            </li>
            <li>(string) <strong>framework</strong>: The framework type relevant to the user. Possible values are:
            	<ul>
                	<li><strong>M</strong> (const: \OUFabric\OUCommon\Models\Qualification::FRAMEWORK_TYPE_M)</li>
                    <li><strong>Q0</strong> (const: \OUFabric\OUCommon\Models\Qualification::FRAMEWORK_TYPE_Q0)</li>
                    <li><strong>Q1</strong> (const: \OUFabric\OUCommon\Models\Qualification::FRAMEWORK_TYPE_Q1)</li>
                    <li><strong>Q2</strong> (const: \OUFabric\OUCommon\Models\Qualification::FRAMEWORK_TYPE_Q2)</li>
                </ul>
            </li>
            <li>(string) <strong>regionCode</strong>: The student's region code</li>
            <li>(string) <strong>pricingCode</strong>: The student's pricing code (See: <a href="Pricing_Area_Codes.xls">Pricing_Area_Codes.xls</a>)</li>
            <li>(array) <strong>qualifications</strong>: An array containing <a href="#model-qualification">Qualification objects</a> that the student is studying</li>
            <li>(array) <strong>modules</strong>: An array containing <a href="#model-module">Module objects</a> that the student is studying</li>
        </ul>
        
        
        
        
      <h2 class="section" id="model-tutor">The Tutor Model</h2>
      <p>The Tutor model represents a tutor at the OU.</p>
      
      <h3>Inheritance</h3>
      <p>The tutor model inherits common traits from the <a href="#model-user">User model</a>.</p>
      
      <h3>Example object</h3>
      <pre>OUFabric\OUGraph\Providers\Tutor Object
(
    [type] => tutor
    [id] => 00432433
    [oucu] => jc27976
    [displayName] => Jack Chapple
    [tutorGroups] => Array
        (
            [0] => OUFabric\OUCommon\Models\TutorGroup Object
                (
                    [module] => OUFabric\OUCommon\Models\Module Object
                        (
                            [name] => Natural and artificial intelligence
                            [code] => M366
                            [level] => undergraduate
                        )

                    [alloc] => 221213
                    [allocCount] => 1
                    [presDate] => 2013-02
                    [presDateOU] => 2013B
                )

            [1] => OUFabric\OUCommon\Models\TutorGroup Object
                (
                    [module] => OUFabric\OUCommon\Models\Module Object
                        (
                            [name] => Natural and artificial intelligence
                            [code] => M366
                            [level] => undergraduate
                        )

                    [alloc] => 224743
                    [allocCount] => 2
                    [presDate] => 2013-02
                    [presDateOU] => 2013B
                )

            [...]

        )

    [isDeferred] => 1
)</pre>

		<h3>Object breakdown</h3>
        <ul>
        	<li>(bool) <strong>isDeferred</strong></li>
            <li>(array) <strong>tutorGroups</strong>: An array of <a href="#model-tutorgroup">TutorGroup object</a> that the tutor manages</li>
        </ul>
        
        
        
        
      <h2 class="section" id="model-staff">The Staff Model</h2>
      <p>The Staff model represents a staff user at the OU.</p>
      
      <h3>Inheritance</h3>
      <p>The staff model inherits common traits from the <a href="#model-user">User model</a>.</p>
      <p>Currently the model has no extra properties or methods.</p>
      
      
      
      
      
      <h2 class="section" id="model-visitor">The Visitor Model</h2>
      <p>The Visitor model represents a visitor user at the OU.</p>
      
      <h3>Inheritance</h3>
      <p>The visitor model inherits common traits from the <a href="#model-user">User model</a>.</p>
      <p>Currently the visitor has no extra properties or methods.</p>
        
        
        
      <h2 class="section" id="model-tutorgroup">The TutorGroup Model</h2>
      <p>The TutorGroup model represents an OU tutor group.</p>
      
      <h3>How to instantiate</h3>
      <p>The TutorGroup model cannot be instantiated as there is currently no data source available to retrieve tutor group information on an individual basis. However, this model is made use of by OU Graph, which uses data about the current viewing user to populate the model.</p>
      
      <h3>Example object</h3>
      <pre>OUFabric\OUCommon\Models\TutorGroup Object
(
    [module] => OUFabric\OUCommon\Models\Module Object
        (
            [name] => Natural and artificial intelligence
            [code] => M366
            [level] => undergraduate
        )

    [alloc] => 224743
    [allocCount] => 2
)</pre>

		<h3>Object breakdown</h3>
        <ul>
        	<li>(object) <strong>module</strong>: A <a href="#model-module">Module object</a> representing the module the tutor group are studying</li>
            <li>(string) <strong>alloc</strong>: Allocation</li>
            <li>(int) <strong>allocCount</strong>: Allocatoun count</li>
        </ul>
      
      
   </div>


<?php
$OUDocs->printFoot();
?>