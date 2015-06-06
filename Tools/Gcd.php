<?php

namespace C33s\CoreBundle\Tools;

class Gcd
{
    protected $numbers = array();

    /**
     * Create a Gcd object to calculate the GCD (Greatest Common Divisor) of the given parameters.
     *
     * The parameters are either given in a single array or as separate arguments.
     * Either way at least 2 values are required to perform the calculation,
     * otherwise an InvalidArgumentException is thrown.
     *
     * The value can then be calculated using the Gcd::calculate() method.
     *
     * @throws \InvalidArgumentException
     *
     * @param mixed $var
     *
     * @return int
     */
    public function __construct($vars)
    {
        if (1 == func_num_args() && is_array($vars) && count($vars) > 1)
        {
            $numbers = $vars;
        }
        elseif (func_num_args() < 2)
        {
            throw new \InvalidArgumentException('Gcd requires either 1 array argument or at least 2 int arguments');
        }
        else
        {
            $numbers = func_get_args();
        }

        foreach ($numbers as $arg)
        {
            $this->numbers[] = abs((int) $arg);
        }
    }

    /**
     * Create a Gcd object to calculate the GCD (Greatest Common Divisor) of the given parameters.
     *
     * The parameters are either given in a single array or as separate arguments.
     * Either way at least 2 values are required to perform the calculation,
     * otherwise an InvalidArgumentException is thrown.
     *
     * This static method directly creates the object and returns the calculated GCD value.
     *
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    public static function initAndCalculate()
    {
        $reflection = new \ReflectionClass('BookMe\CoreBundle\Tools\Gcd');
        $gcd        = $reflection->newInstanceArgs(func_get_args());

        return $gcd->calculate();
    }

    /**
     * Perform the actual GCD calculation.
     *
     * @return int
     */
    public function calculate()
    {
        $gcd = $this->numbers[0];

	$numberCount = count($this->numbers);

        for ($i = 1; $i < $numberCount; ++$i)
        {
            $gcd = $this->calculatePair($gcd, $this->numbers[$i]);
        }

        return $gcd;
    }

    /**
     * Calculate the GCD of 2 positive integers using the recursive Euclidean Algorithm.
     *
     * see https://en.wikipedia.org/wiki/Euclidean_algorithm#Implementations
     *
     * @param int $a
     * @param int $b
     *
     * @return int
     */
    protected function calculatePair($a, $b)
    {
        if (0 == $b)
        {
            return $a;
        }

        return $this->calculatePair($b, $a % $b);
    }
}
