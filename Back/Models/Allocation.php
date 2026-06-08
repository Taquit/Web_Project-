<?php

class Allocation {
    // Atributos conexion
    private $conn;
    private $table_name = "Allocation";

    // Propiedades de Allocation
    public $id_allocation;
    public $no_boleta;
    public $id_lab;
    public $id_schedule;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Insertar una nueva asignacion
    public function create_Allocation() {
        $query = "INSERT INTO " . $this->table_name . "
            (id_allocation, no_boleta, id_lab, id_schedule)
            VALUES
            (:id_allocation, :no_boleta, :id_lab, :id_schedule)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":id_allocation", $this->id_allocation);
        $stmt->bindParam(":no_boleta",     $this->no_boleta);
        $stmt->bindParam(":id_lab",        $this->id_lab);
        $stmt->bindParam(":id_schedule",   $this->id_schedule);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Obtener todas las asignaciones
    public function get_All_Allocations() {
        $query = "SELECT 
                    a.id_allocation,
                    a.no_boleta,
                    s.name_student,
                    s.last_name_P,
                    s.last_name_M,
                    l.name AS lab_name,
                    l.capacity,
                    sc.start_time,
                    sc.end_time,
                    sc.exam_date
                  FROM " . $this->table_name . " a
                  INNER JOIN Student   s  ON a.no_boleta   = s.no_boleta
                  INNER JOIN Lab       l  ON a.id_lab      = l.id_lab
                  INNER JOIN Schedule  sc ON a.id_schedule = sc.id_schedule";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    // Obtener asignacion por id
    public function get_By_Id($id_to_find) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_allocation = :id_allocation LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_allocation", $id_to_find);
        $stmt->execute();

        return $stmt;
    }

    // Obtener asignaciones de un alumno especifico
    public function get_By_Boleta($boleta_to_find) {
        $query = "SELECT 
                    a.id_allocation,
                    l.name AS lab_name,
                    sc.start_time,
                    sc.end_time,
                    sc.exam_date
                  FROM " . $this->table_name . " a
                  INNER JOIN Lab      l  ON a.id_lab      = l.id_lab
                  INNER JOIN Schedule sc ON a.id_schedule = sc.id_schedule
                  WHERE a.no_boleta = :no_boleta";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":no_boleta", $boleta_to_find);
        $stmt->execute();

        return $stmt;
    }

    // Eliminar una asignacion por id
    public function delete_Allocation($id_to_delete) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_allocation = :id_allocation";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_allocation", $id_to_delete);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}

?>
