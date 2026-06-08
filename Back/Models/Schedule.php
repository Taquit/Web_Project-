<?php

class Schedule {
    // Atributos conexion
    private $conn;
    private $table_name = "Schedule";

    // Propiedades de Schedule
    public $id_schedule;
    public $start_time;
    public $end_time;
    public $exam_date;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Insertar un nuevo horario
    public function create_Schedule() {
        $query = "INSERT INTO " . $this->table_name . "
            (id_schedule, start_time, end_time, exam_date)
            VALUES
            (:id_schedule, :start_time, :end_time, :exam_date)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_schedule", $this->id_schedule);
        $stmt->bindParam(":start_time",  $this->start_time);
        $stmt->bindParam(":end_time",    $this->end_time);
        $stmt->bindParam(":exam_date",   $this->exam_date);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Obtener todos los horarios
    public function get_All_Schedules() {
        $query = "SELECT * FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Obtener un horario por id
    public function get_By_Id($id_to_find) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_schedule = :id_schedule LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_schedule", $id_to_find);
        $stmt->execute();

        return $stmt;
    }

    // Eliminar un horario por id
    public function delete_Schedule($id_to_delete) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_schedule = :id_schedule";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_schedule", $id_to_delete);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}

?>
