<?php

$pirates = [
    'Blackbeard' => [
        'defense' => 1,
        'damage' => 1.1
    ],
    'Popeye' => [
        'defense' => 1.1,
        'damage' => 1
    ],
    'Spock' => [
        'defense' => 1.5,
        'damage' => 1.5
    ]
];

class Crew
{

    private $crew_list;

    private $vessel_name;

    public function __construct($crew = [])
    {

        $this->crew_list = $crew;
    }
    
    public function set_name($name) {
        $this->vessel_name = $name;
    }

    public function crew_list()
    {

        if (!count($this->crew_list)) {
            echo "You have no special crew members \n";
            return $this->crew_list;
        }

        echo "The crew members of the $this->vessel_name are: \n";

        foreach ($this->crew_list as $sailor => $stats) {
            $damage = $stats['damage'];
            $defense = $stats['defense'];
            echo "    $sailor
                damage  modifier: $damage
                defense modifier: $defense \n";
        };

        return $this->crew_list;
    }

    public function recruit(&$pirates)
    {
        if (!count($pirates)) {
            echo "Sadly there are no sailors looking for work \n";
            return 0;
        }

        $new_sailor = array_rand($pirates);
        $this->crew_list[$new_sailor] = $pirates[$new_sailor];
        unset($pirates[$new_sailor]);
        echo "Congratulations $this->vessel_name, you've recruited $new_sailor \n";
        return 0;
    }


    public function injury()
    {

        if (rand(0, 100) >= 10) {
            $dead_sailor = array_rand($this->crew_list);
            unset($this->crew_list[$dead_sailor]);
            echo "unlucky $this->vessel_name, your crew member $dead_sailor has died \n";
        }
    }

    public function modifier()
    {

        $defense_mod = 1;
        $damage_mod = 1;

        foreach ($this->crew_list as $crew) {
            $defense_mod *= $crew['defense'];
            $damage_mod *= $crew['damage'];
        };

        return  ['defense' => $defense_mod, 'damage' => $damage_mod];
    }
}


abstract class Ship
{
    // Abstract battleship class containing attack and is_hit methods
    private $health = 100;

    private $crew;

    private $name;
    
    // damage function is required to be defined for any concrete class based on 
    // the abstract Ship class. This value is used in the calculation of attack 
    // method results.
    abstract public function damage();

    // defense function is required to be defined for any concrete class based on 
    // the abstract Ship class. This value is used in the calculation of attack 
    // method results.
    abstract public function defense();

    // health variable is initially set to 100. On reaching 0, the ship is considered
    // destroyed
    public function health()
    {
        return $this->health;
    }
    
    public function crew() 
    {
        return $this->crew;
    }

    public function __construct(string $name = "Tinkerbell", Crew $crew)
    {

        $this->name = $name;

        $this->crew = $crew;
        
        $this->crew->set_name($name);

        echo "The " . get_class($this) . " $this->name leaves the dry dock! \n";
    }


    public function is_hit($points)
    {
        // reduces the health points available to the ship by `points`. The health 
        // of the ship is then checked, and if found to be non-positive the 
        // `destroyed` variable is updated to true, preventing further attacks by 
        // or against this ship.

        $this->health -= $points;

        return 0;
    }

    public function sinks($enemy)
    {
        echo 'The ' . get_class($enemy) . " $enemy->name is sunk by the "
            . get_class($this) . " $this->name \n";
        unset($enemy);
    }

    public function attacks($enemy)
    {
        // A public method to initiate the reduction of the health of an enemy ship.
        // several checks are first undertaken to ensure that the ship exists, 
        // that the attack is not self-directed, and that the ship you are attempting to 
        // initiate an attack from has not been destroyed.        

        if (!isset($enemy)) {

            echo "The ship you are trying to attack does not exist... \n";

            return -1;
        } elseif ($enemy == $this) {

            echo "You cannot fire on your own ship! \n";

            return -1;
        }
        // Once checks are complete, random numbers accuracy and luck are generated.
        // accuracy has value 0 with probability 1/4, which condition causes the 
        // shot to 'miss' and inflict no damage. Luck has value 9 with probability 1/10,
        // which condition causes the shot to be 'lucky' and cause triple damage.
        // Attacker damage and enemy defense values are then used to calculate the
        // total damage of each shot, after which the damage is inflicted with 
        // the is-hit method of the enemy ship.


        // attack and defense multipliers due to crew members are used to 
        // modify attack result.

        $ourModifier = $this->crew->modifier();
        $theirModifier = $enemy->crew->modifier();

        $damage = $this->damage();
        $defense = $enemy->defense();

        $defense *= $theirModifier['defense'];
        $damage *= $ourModifier['damage'];

        $luck = rand(0, 100);
        $accuracy = rand(0, 100);

        switch ([$luck >= 75, $accuracy >= 10]) {

            case [true, true]:
                $points = 3 * $damage * (25 / $defense);
                echo 'Lucky hit. The ' . get_class($this) . " " . $this->name  . ' inflicts ';
                echo $points . ' damage against the ' . get_class($enemy) . " " . $enemy->name . "\n";
                $enemy->is_hit($points);
                break;

            case [false, true]:
                $points = $damage * (25 / $defense);
                echo 'Standard hit. The ' . get_class($this) . " " . $this->name  . ' inflicts ';
                echo $points . ' damage against the ' . get_class($enemy) . " " . $enemy->name . "\n";
                $enemy->is_hit($points);
                break;

            default:
                echo "Unlucky $this->name, your attack against the " . get_class($enemy) . " $enemy->name misses. \n";
        }
        if ($enemy->health() < 0) {
            $this->sinks($enemy);
            return 1;
        }
        return 0;
    }
}


class Destroyer extends Ship {
    // Concrete Destroyer class, medium damage and high defense

    function damage() {
        return 15;
    }

    function defense() {
        return 50;
    }
}

class Carrier extends Ship {
    // Concrete Carrier class, high damage and medium defense

    function damage() {
        return 30;
    }

    function defense() {
        return 30;
    }
}

class Trawler extends Ship {
    // Concrete Trawler class, no damage and minimal defense

    function damage() {
        return 0;
    }

    function defense() {
        return 5;
    }
}
// $test = new Destroyer('scrappy');

// $test->crew->recruit($pirates);
// $test->crew->recruit($pirates);
// $test->crew->crew_list();

// $test2 = new Carrier('teeny');
// $test2->crew->recruit($pirates);
// $test2->attacks($test);
// $test2->crew->crew_list();
