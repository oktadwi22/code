<?php
namespace App\Traits;

use App\Constants\Status;

trait UserNotify
{
    public static function notifyToUser(){
        return [
            'allAuthors'                => 'All Authors',
            'allUsers'                  => 'All Users',
            'authorsHasNoProducts'      => 'Authors with No Products',
            'authorsHasPendingProducts' => 'Authors with Pending Products',
            'selectedUsers'             => 'Selected Users',
            'kycUnverified'             => 'Kyc Unverified Users',
            'kycVerified'               => 'Kyc Verified Users',
            'kycPending'                => 'Kyc Pending Users',
            'withBalance'               => 'With Balance Users',
            'emptyBalanceUsers'         => 'Empty Balance Users',
            'twoFaDisableUsers'         => '2FA Disable User',
            'twoFaEnableUsers'          => '2FA Enable User',
            'hasDepositedUsers'         => 'Paid Users',
            'notDepositedUsers'         => 'Users with No Payment',
            'pendingDepositedUsers'     => 'Users with Pending Payment',
            'rejectedDepositedUsers'    => 'Users with Rejected Payment',
            'topDepositedUsers'         => 'Top Paid Users',
            'hasWithdrawUsers'          => 'Withdraw Users',
            'pendingWithdrawUsers'      => 'Pending Withdraw Users',
            'rejectedWithdrawUsers'     => 'Rejected Withdraw Users',
            'pendingTicketUser'         => 'Pending Ticket Users',
            'answerTicketUser'          => 'Answer Ticket Users',
            'closedTicketUser'          => 'Closed Ticket Users',
            'notLoginUsers'             => 'Last Few Days Not Login Users',
        ];
    }

    public function authorsHasNoProducts($query) {
        return $query->author()->whereDoesntHave('products', function($q) {
            $q->approved();
        });
    }

    public function authorsHasPendingProducts($query) {
        return $query->author()->whereHas('products', function($q) {
            $q->pending();
        });
    }

    public function scopeSelectedUsers($query)
    {
        return $query->whereIn('id', request()->user ?? []);
    }

    public function scopeAllAuthors($query)
    {
        return $query->author();
    }

    public function scopeAllUsers($query)
    {
        return $query->where('is_author', Status::NO);
    }

    public function scopeEmptyBalanceUsers($query)
    {
        return $query->where('balance', '<=', 0);
    }

    public function scopeTwoFaDisableUsers($query)
    {
        return $query->where('ts', Status::DISABLE);
    }

    public function scopeTwoFaEnableUsers($query)
    {
        return $query->where('ts', Status::ENABLE);
    }

    public function scopeHasDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        });
    }

    public function scopeNotDepositedUsers($query)
    {
        return $query->whereDoesntHave('deposits', function ($q) {
            $q->successful();
        });
    }

    public function scopePendingDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->pending();
        });
    }

    public function scopeRejectedDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->rejected();
        });
    }

    public function scopeTopDepositedUsers($query)
    {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        })->withSum(['deposits'=>function($q){
            $q->successful();
        }], 'amount')->orderBy('deposits_sum_amount', 'desc')->take(request()->number_of_top_deposited_user ?? 10);
    }

    public function scopeHasWithdrawUsers($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->approved();
        });
    }

    public function scopePendingWithdrawUsers($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->pending();
        });
    }

    public function scopeRejectedWithdrawUsers($query)
    {
        return $query->whereHas('withdrawals', function ($q) {
            $q->rejected();
        });
    }

    public function scopePendingTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY]);
        });
    }

    public function scopeClosedTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', Status::TICKET_CLOSE);
        });
    }

    public function scopeAnswerTicketUser($query)
    {
        return $query->whereHas('tickets', function ($q) {

            $q->where('status', Status::TICKET_ANSWER);
        });
    }

    public function scopeNotLoginUsers($query)
    {
        return $query->whereDoesntHave('loginLogs', function ($q) {
            $q->whereDate('created_at', '>=', now()->subDays(request()->number_of_days ?? 10));
        });
    }

    public function scopeKycVerified($query)
    {
        return $query->where('kv', Status::KYC_VERIFIED);
    }

}
