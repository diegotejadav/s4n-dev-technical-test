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
    private $position_x;
    private $position_y;
    /**
     * Cardinal points:
     * N: North, S: South, E: East, W: West
     */
    private $orientation;

    public function __construct($paths, $maxDeliveriesPerTime)
    {
        $this->paths = $paths;
        $this->maxDeliveriesPerTime = $maxDeliveriesPerTime;
        $this->position_x = 0;
        $this->position_y = 0;
        $this->orientation = 'N';
    }

    /**
     * Starts the delivery for each path
     */
    public function startDelivery($outputFileName)
    {
        $deliveriesCount = 0;
        $deliveriesResults = ['== Reporte de entregas =='];

        // Process each delivery
        foreach ($this->paths as $path) {
            $deliveriesCount++;
            $deliverResult = $this->deliverPath($path);
            if ($deliverResult) {
                $longOrientation = static::getLongOrientation($this->orientation);
                echo("($this->position_y, $this->position_x) $longOrientation\n");
                $deliveriesResults[] = "($this->position_y, $this->position_x) $longOrientation";
            } else {
                echo("Error en la entrega\n");
                $deliveriesResults[] = "Error en la entrega";
            }

            // Maximum capacity reached, so go back to the restaurant
            if ($deliveriesCount == $this->maxDeliveriesPerTime) {
                $this->goHome();
            }
        }

        // Save the results
        Util::saveResults($outputFileName, $deliveriesResults);
        echo "Started...";
        return 1;
    }

    /**
     * Delivers a single path following each of its instructions
     * Example:
     * AAAAIAA
     * 
     * A: Means a forward motion
     * I: Means turn to the left (90º)
     * D: Means turn to the right (90º)
     */
    private function deliverPath($path)
    {
        // TODO: Validate each instruction
        // The whole string only can contain letters: A, I or D
        $instructions = str_split($path);

        $delivered = true;
        foreach ($instructions as $instruction) {
            $result = $this->processInstruction($instruction);
            if (!$result['success']) {
                $delivered = false;
                break;
            }
        }

        return $delivered;
    }

    /**
     * Process an instruction with the Dron
     * @return Array if the instruction is valid and was executed successfully
     */
    public function processInstruction($instruction)
    {
        if (!in_array($instruction, ['A', 'I', 'D'])) {
            return [
                'success' => false,
                'error' => 'Invalid operation',
            ];
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

        return ['success' => true];
    }

    /**
     * Moves the dron 1 step forward according to its orientation (N, S, E, W)
     */
    public function moveForward()
    {
        switch ($this->orientation) {
            case 'N':
                $this->position_x += 1;
                break;
            case 'W':
                $this->position_y -= 1;
                break;
            case 'S':
                $this->position_x -= 1;
                break;
            case 'E':
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