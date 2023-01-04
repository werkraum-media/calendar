TYPO3 Extension: calendar
=========================

Provides:

* Data (classes) for Year, Month, Week and Day.

* Controller with action to view Year, Month, Week and Day.

Each day can have foreign data created by a factory.
That way extensions or TYPO3 instances can add further data to each day.

Alter Variables
---------------

The controller also has an event to alter assigned variables for each action.

Check out ``Tests/Fixtures/calendar_example/`` as an example on how to provide
necessary custom setup.

Configuration
-------------

Allows to configure default values for arguments if not provided in current request.
Each argument is configured below TypoScript settings namespace `arguments`, e.g.::

    tx_calendar_example {
        settings {
            arguments {
                year {
                    strtotime = midnight first day of -1 year
                    strftime = %Y
                }
            }
        }
    }

Supported arguments are: `year`, `month`, `week` and `day`.
