# Scheduled Scan Flow

```
┌──────────┐     ┌───────────┐     ┌──────────────┐
│   User   │────>│ Dashboard │────>│ Schedule     │
│ (Logged  │     │           │     │ Scan Form    │
│   In)    │     │           │     │              │
└──────────┘     └───────────┘     └──────┬───────┘
                                          │
                                    Enter URL +
                                    Frequency
                                          │
                                          ▼
                                   ┌─────────────┐
                                   │  Validate   │
                                   │  URL +      │
                                   │  Frequency  │
                                   └──────┬──────┘
                                          │
                                   ┌──────┴──────┐
                                   ▼             ▼
                             ┌──────────┐  ┌──────────┐
                             │  Valid   │  │ Invalid  │
                             └────┬─────┘  └────┬─────┘
                                  │             │
                                  │             ▼
                                  │       ┌──────────┐
                                  │       │  Show    │
                                  │       │  Errors  │
                                  │       └──────────┘
                                  ▼
                           ┌─────────────┐
                           │   Create    │
                           │ ScanSchedule│
                           │  Record     │
                           └──────┬──────┘
                                  │
                                  ▼
                           ┌─────────────┐
                           │ Set Next    │
                           │ Run: +1 Hour│
                           └──────┬──────┘
                                  │
                                  ▼
                    ┌─────────────────────────────┐
                    │     Cron (Scheduled Task)   │
                    │   RunScheduledScans Command │
                    └──────────────┬──────────────┘
                                   │
                            ┌──────┴──────┐
                            ▼             ▼
                      ┌──────────┐  ┌──────────┐
                      │ Active + │  │ Skip     │
                      │ Due Now  │  │ (Not Due │
                      │          │  │ or Paused│
                      └────┬─────┘  └──────────┘
                           │
                           ▼
                    ┌─────────────┐
                    │  Dispatch   │
                    │ RunScanJob  │
                    └──────┬──────┘
                           │
                           ▼
                    ┌─────────────┐     ┌─────────────┐
                    │  Pa11y     │────>│  Store      │
                    │  Scans     │     │  Results    │
                    │  Pages     │     │  + Score    │
                    └─────────────┘     └──────┬──────┘
                                               │
                                               ▼
                                        ┌─────────────┐
                                        │ Update Next │
                                        │ Run Based   │──── daily/weekly/monthly
                                        │ on Frequency│
                                        └─────────────┘
```

---
*Generated with /ascii*
