<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

class TransactionPolicy
{
    /**
     * Determine whether the user can view the transaction.
     */
    public function view(User $user, Transaction $transaction): bool
    {
        // Buyer bisa view transaksi mereka sendiri
        if ($user->id === $transaction->buyer_id) {
            return true;
        }

        // Seller bisa view transaksi dari kantin mereka
        if ($user->isSeller() && $transaction->canteen->sellers->contains($user)) {
            return true;
        }

        // Manager bisa view semua transaksi
        if ($user->isManager()) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether user can cancel transaction.
     */
    public function cancel(User $user, Transaction $transaction): bool
    {
        // Hanya buyer sendiri yang bisa batalin
        return $user->id === $transaction->buyer_id
            && ($transaction->isCancellableByBuyerFreely() || $transaction->isCancellableWithSellerApproval());
    }

    /**
     * Determine whether user can confirm transaction (pesanan sudah diambil).
     */
    public function confirm(User $user, Transaction $transaction): bool
    {
        // Hanya buyer yang bisa confirm
        return $user->id === $transaction->buyer_id && $transaction->isReady();
    }

    /**
     * Determine whether user can pay for transaction.
     */
    public function pay(User $user, Transaction $transaction): bool
    {
        // Hanya buyer sendiri yang bisa bayar
        return $user->id === $transaction->buyer_id && $transaction->isAccepted();
    }

    /**
     * Determine whether user can give review for transaction.
     */
    public function review(User $user, Transaction $transaction): bool
    {
        // Hanya buyer sendiri, transaksi done, dan belum di-review
        return $user->id === $transaction->buyer_id && $transaction->isReviewable();
    }
}
