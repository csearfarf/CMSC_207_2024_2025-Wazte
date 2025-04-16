<?php
if (!function_exists('getRolename')) {
    /**
     * Get the role name based on the role ID.
     *
     * @param int $role
     * @return string
     */
    function getRolename(int $role): string
    {
        switch ($role) {
            case 1:
                return "Admin";
            case 2:
                return "Facilitator";
            case 3:
                return "User";
            case 4:
                return "Blank";
            default:
                return "";
        }
    }
}
