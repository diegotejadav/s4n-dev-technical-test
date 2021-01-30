<?php

/**
 * Class for manage Drones
 * @author Diego Tejada <diegotejadav@gmail.com>
 */
class Dron
{
    /**
     * Deliveries to be processed
     */
    private $paths;
    private $maxDeliveriesPerTime;
    private $maxCoverage;
    private $position_x;
    private $position_y;
    /**
     * Cardinal points:
     * N: North, S: South, E: East, W: West
     */
    private $orientation;

    public function __construct($paths, $maxDeliveriesPerTime, $maxCoverage, $outputFileName)
    {
        $this->paths = $paths;
        $this->maxDeliveriesPerTime = $maxDeliveriesPerTime;
        $this->maxCoverage = $maxCoverage;
        $this->outputFileName = $outputFileName;
        $this->position_x = 0;
        $this->position_y = 0;
        $this->orientation = 'N';
    }

    /**
     * Starts the delivery for each path
     */
    public function startDelivery()
    {
        $deliveriesCount = 0;
        $deliveriesResults = ['== Reporte de entregas =='];

        // Process each delivery
        foreach ($this->paths as $path) {
            $deliveriesCount++;
            try {
                $this->deliverPath($path);
                $longOrientation = static::getLongOrientation($this->orientation);
                $deliveriesResults[] = "($this->position_y, $this->position_x) $longOrientation";
            } catch (Exception $e) {
                $deliveriesResults[] = "Error en la entrega: " . $e->getMessage();
            }

            // Maximum capacity reached, so go back to the restaurant
            if ($deliveriesCount == $this->maxDeliveriesPerTime) {
                $this->goHome();
            }
        }

        // Save the results
        Util::saveResults($this->outputFileName, $deliveriesResults);
    }

    /**
     * Delivers a single path following each of its instructions
     * Example:
     * AAAAIAA
     * 
     * A: Means a forward motion
     * I: Means turn to the left (90º)
     * D: Means turn to the right (90º)
     * @param String $path the path to follow
     * @return Boolean if the path was successfully completed
     */
    public function deliverPath($path)
    {
        // The whole string only can contain letters: A, I or D
        $instructions = str_split($path);

        foreach ($instructions as $instruction) {
            try {
                $this->processInstruction($instruction);
            } catch (InvalidArgumentException $e) {
                throw new Exception($e->getMessage());
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        return true;
    }

    /**
     * Process an instruction with the Dron
     * @param String $instruction the instruction to apply to the dron
     * @return Boolean if the instruction is valid and was executed successfully
     * @throws InvalidArgumentException
     */
    public function processInstruction($instruction)
    {
        if (!in_array($instruction, ['A', 'I', 'D'])) {
            throw new InvalidArgumentException("Invalid operation");
        }

        switch ($instruction) {
            case 'A':
                $this->moveForward();
                break;
            case 'I':
                $this->turnLeft();
                break;
            case 'D':
                $this->turnRight();
                break;
        }

        return true;
    }

    /**
     * Moves the dron 1 step forward according to its orientation (N, S, E, W)
     * @throws Exception
     */
    public function moveForward()
    {
        switch ($this->orientation) {
            case 'N':
                // If reached max coverage
                if ($this->position_x == $this->maxCoverage) {
                    throw new Exception("Maximum coverage reached: $this->orientation");
                }
                $this->position_x += 1;
                break;
            case 'W':
                if ($this->position_y == $this->maxCoverage * -1) {
                    throw new Exception("Maximum coverage reached: $this->orientation");
                }
                $this->position_y -= 1;
                break;
            case 'S':
                if ($this->position_x == $this->maxCoverage * -1) {
                    throw new Exception("Maximum coverage reached: $this->orientation");
                }
                $this->position_x -= 1;
                break;
            case 'E':
                if ($this->position_y == $this->maxCoverage) {
                    throw new Exception("Maximum coverage reached: $this->orientation");
                }
                $this->position_y += 1;
                break;
        }
    }

    /**
     * Turns the dron left
     * Updates the drone left orientation according it's current orientation
     */
    public function turnLeft()
    {
        $leftOrientationMap = ['N' => 'W', 'W' => 'S', 'S' => 'E', 'E' => 'N'];
        $this->orientation = $leftOrientationMap[$this->orientation];
    }

    /**
     * Turns the dron right
     * Updates the drone left orientation according it's current orientation
     */
    public function turnRight()
    {
        $rightOrientationMap = ['N' => 'E', 'E' => 'S', 'S' => 'W', 'W' => 'N'];
        $this->orientation = $rightOrientationMap[$this->orientation];
    }

    /**
     * Returns the long description of the dron orientation
     * @return String
     */
    public static function getLongOrientation($orientation)
    {
        $orientationMap = [
            'N' => 'dirección Norte',
            'S' => 'dirección Sur',
            'E' => 'dirección Oriente',
            'W' => 'dirección Occidente',
        ];

        return $orientationMap[$orientation];
    }

    /**
     * Returns the current position of the drone
     * For example
     * [-1, 4, 'N']
     * @return Array the drone position in Array format: [Y, X, Orientation]
     */
    public function getCurrentPosition()
    {
        return [$this->position_y, $this->position_x, $this->orientation];
    }

    /**
     * Resets the dron position
     */
    public function goHome()
    {
        $this->position_x = 0;
        $this->position_y = 0;
        $this->orientation = 'N';
    }
}

?>