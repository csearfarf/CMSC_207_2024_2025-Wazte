<?php
namespace App\Models;

use CodeIgniter\Model;

/**
 * UserModel
 *
 * This model handles user data operations for the WAZTE system.
 * It includes functions to check registration status, update, insert, and delete user records.
 */
class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $DBGroup       = 'default';
    // Include roleID in allowedFields so it can be inserted.
    protected $allowedFields = ['oauth_id', 'name', 'email', 'profile_img', 'updated_at', 'created_at', 'roleID'];

    /**
     * Check if a user is already registered by their OAuth ID.
     *
     * @param string $authid The OAuth ID to check.
     * @return bool True if the user is already registered, false otherwise.
     */
    function isAlreadyRegister($authid) {
        $row = $this->db->table($this->table)
                        ->getWhere(['oauth_id' => $authid])
                        ->getRowArray();
        return !empty($row);
    }

    /**
     * Check if a user is already registered using their email address.
     *
     * @param string $byEmail The email address to check.
     * @return bool True if a user with the specified email exists, false otherwise.
     */
    function isAlreadyRegisterByEmail($byEmail) {
        $row = $this->db->table($this->table)
                        ->getWhere(['email' => $byEmail])
                        ->getRowArray();
        return !empty($row);
    }

    /**
     * Update user data based on the provided OAuth ID.
     *
     * @param array $userdata The data to update.
     * @param string $authid The OAuth ID used to identify the user.
     * @return void
     */
    function updateUserData($userdata, $authid) {
        $this->db->table($this->table)
                 ->where(['oauth_id' => $authid])
                 ->update($userdata);
    }

    /**
     * Insert new user data for an undecided user.
     *
     * Automatically sets roleID to "4" to indicate that the user is undecided.
     *
     * @param array $userdata The new user data to insert.
     * @return void
     */
    function insertUserData($userdata) {
        // Set roleID = "4" only when inserting new user data, since they are undecided.
        $userdata['roleID'] = "4";
        $this->db->table($this->table)
                 ->insert($userdata);
    }

    /**
     * Retrieve the role of a user by their OAuth ID.
     *
     * @param string $authid The OAuth ID of the user.
     * @return mixed The user's roleID if found, otherwise null.
     */
    function getUserRole($authid) {
        $row = $this->db->table($this->table)
                        ->getWhere(['oauth_id' => $authid])
                        ->getRowArray();
        return !empty($row) ? $row['roleID'] : null;
    }

    /**
     * Insert new user data for admin role.
     *
     * This function can be used for inserting new user records from the admin interface.
     *
     * @param array $userdata The data for the new user.
     * @return void
     */
    function insertNewUserData($userdata) {
        $this->db->table($this->table)
                 ->insert($userdata);
    }

    /**
     * Delete a user by id.
     *
     * This function deletes a user record based on the provided user id.
     *
     * @param string $id The user's  id.
     * @return bool True if deletion was successful, false otherwise.
     */
    public function deleteUserByID(int $id): bool
    {
        return $this->db->table($this->table)
                        ->where(['user_ID' => $id])
                        ->delete();
    }

    /**
     * Retrieve a user record by id.
     *
     * This function fetches a user record based on the provided user id.
     *
     * @param int $id The user's id.
     * @return array|null The user details as an associative array if found, null otherwise.
     */
    public function viewUserByID(int $id): ?array
    {
        return $this->db->table($this->table)
                        ->where(['user_ID' => $id])
                        ->get()
                        ->getRowArray();
    }

    /**
     * Retrieve a user record by email.
     *
     * This function fetches a user record based on the provided user email.
     *
     * @param string $email The user's email.
     * @return array|null The user details as an associative array if found, null otherwise.
     */
    public function viewUserByEmail(string $email): ?array
    {
        return $this->db->table($this->table)
                        ->where(['email' => $email])
                        ->get()
                        ->getRowArray();
    }

     /**
     * Update user data based on the provided userid.
     *
     * @param array $userdata The data to update.
     * @param string $userid The userid used to identify the user.
     * @return void
     */
    public function updateUserDataByID(int $id, array $userdata): bool {
        return $this->db->table($this->table)
                        ->where(['user_ID' => $id])
                        ->update($userdata);
    }
    

}
