<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Console\Command;

class GrantUserPlan extends Command
{
    protected $signature = 'user:grant-plan
                            {email : The user\'s email address}
                            {plan : The plan slug (monthly or lifetime)}';

    protected $description = 'Grant a user access to a paid plan without payment';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error("No user found with email: {$this->argument('email')}");

            return self::FAILURE;
        }

        $planSlug = $this->argument('plan');
        $plan = Plan::where('slug', $planSlug)->first();

        if (! $plan) {
            $this->error("Invalid plan: {$planSlug}. Use 'monthly' or 'lifetime'.");

            return self::FAILURE;
        }

        $user->update([
            'plan' => $plan->slug,
            'scan_limit' => $plan->scan_limit,
        ]);

        $this->info("Granted {$plan->name} plan to {$user->name} ({$user->email})");
        $this->line("  Scan limit: {$plan->scan_limit}/month");
        $this->line("  Pages per scan: {$plan->page_limit_per_scan}");

        return self::SUCCESS;
    }
}
