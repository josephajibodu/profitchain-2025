<?php

namespace App\Enums;

use App\Traits\HasValues;

enum SystemPermissions: string
{
    use HasValues;

    case ManageUsers = 'manage users';

    case BanUsers = 'ban users';

    case ManageTransfers = 'manage transfers';

    case ManageDonations = 'manage donations';

    case ManageOrders = 'manage orders';

    case ManageAdverts = 'manage sell adverts';

    case ManageSettings = 'manage settings';

    case ManageKYC = 'manage kyc';

    case ViewAllDisputes = 'manage disputes';

    case AccessDashboard = 'access dashboard';

    case ManageFunds = 'manage funds';

    case AssignRoles = 'assign roles';
}