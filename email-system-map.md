# AccessScan Email System Map

```
                            ┌──────────────────────────────────────┐
                            │        AccessScan Email System       │
                            └──────────────────┬───────────────────┘
                                               │
              ┌────────────────────────────────┬┴───────────────────────────────┐
              ▼                                ▼                                ▼
   ┌─────────────────┐             ┌─────────────────────┐          ┌────────────────────┐
   │   LIFECYCLE      │             │   SCAN ACTIVITY     │          │   BILLING          │
   └────────┬────────┘             └─────────┬───────────┘          └─────────┬──────────┘
            │                                │                                │
            ▼                                ▼                                ▼
   ┌─────────────────┐             ┌─────────────────────┐          ┌────────────────────┐
   │  WelcomeMail    │             │  ScanCompleteMail   │          │ TrialExpiringMail  │
   │  (on signup)    │             │  (scan finished)    │          │ (trial ending soon)│
   └────────┬────────┘             └─────────┬───────────┘          └─────────┬──────────┘
            │                                │                                │
            ▼                                ▼                                ▼
   ┌─────────────────┐             ┌─────────────────────┐          ┌────────────────────┐
   │ PlanBenefitMail │             │ FirstIssueFixMail   │          │ TrialExpiredMail   │
   │ (upgrade nudge) │             │ (1st fix detected)  │          │ (trial ended)      │
   └────────┬────────┘             └─────────┬───────────┘          └─────────┬──────────┘
            │                                │                                │
            ▼                                ▼                                ▼
   ┌─────────────────┐             ┌─────────────────────┐          ┌────────────────────┐
   │ReEngagementMail │             │ ScoreImproveMail    │          │ PaymentFailedMail  │
   │ (inactive user) │             │ (score went up)     │          │ (charge failed)    │
   └─────────────────┘             └─────────┬───────────┘          └────────────────────┘
                                             │
                                             ▼
                                   ┌─────────────────────┐
                                   │RegressionAlertMail  │
                                   │ (score went down)   │
                                   └─────────┬───────────┘
                                             │
                                             ▼
                                   ┌─────────────────────┐
                                   │ WeeklyDigestMail    │
                                   │ (weekly summary)    │
                                   └─────────────────────┘
```

## Summary

| Category | Email | Trigger | Preview |
|---|---|---|---|
| **Lifecycle** | WelcomeMail | User signs up | `/mailable/welcome` |
| | PlanBenefitMail | Nudge to upgrade plan | `/mailable/plan-benefit` |
| | ReEngagementMail | User inactive | `/mailable/re-engagement` |
| **Scan Activity** | ScanCompleteMail | Scan finishes | `/mailable/scan-complete` |
| | FirstIssueFixMail | First accessibility fix detected | `/mailable/first-issue-fix` |
| | ScoreImproveMail | Score improves | `/mailable/score-improve` |
| | RegressionAlertMail | Score regresses | `/mailable/regression-alert` |
| | WeeklyDigestMail | Weekly summary | `/mailable/weekly-digest` |
| **Billing** | TrialExpiringMail | Trial ending soon | `/mailable/trial-expiring` |
| | TrialExpiredMail | Trial ended | `/mailable/trial-expired` |
| | PaymentFailedMail | Payment charge failed | `/mailable/payment-failed` |

> **Note:** Preview links are only available in the `local` environment.

---
*Generated with /ascii*
