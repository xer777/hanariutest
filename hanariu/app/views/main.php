<?php 
//print_r(get_declared_classes());
//die;
//$date = new \ExpressiveDate;

// Use the static make method to get an instance of Expressive Date.
//$date = \ExpressiveDate::make();
// echo $date->addOneYear();

 /*use \Cabinet\DBAL\Db;
 $connection = Db::connection(array(
			'driver' => 'mysql',
			'username' => 'root',
			'password' => 'vertrigo',
			'database' => 'laravel',
		));

 $query = $connection->select()->from('comments')->execute(); //->compile();
echo "<pre>";
var_dump($query);
echo "</pre>";
 */

//use \Respect\Validation\Validator as v;
/*$user = new \stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';

$userValidator = \Respect\Validation\Validator::attribute('name', v::string()->length(1,32))
                  ->attribute('birthdate', v::date()->minimumAge(18));

echo  $userValidator->validate($user); //true
echo "<br />";
echo "<br />";*/
/*use \Respect\Relational\Mapper;
$mapper = new Mapper(new \PDO('mysql:host=127.0.0.1;port=3306;dbname=laravel','root','vertrigo'));
$comments = $mapper->comments->fetchAll();
echo "<pre>";
var_dump($comments);
echo "</pre>";*/

/*$faker = \Faker\Factory::create();
echo $faker->name;
echo $faker->address;
echo "<br />";
echo "<br />";
$x = Valid::url('http://www.onet.pl');

echo "<pre>";
var_dump($x);
echo "</pre>";
*/
?>
dziala :)
