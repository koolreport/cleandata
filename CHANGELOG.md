# Version 1.7.0-dev
1. `FillNull` accepts the column => new value shorthand: `new FillNull(array("state" => "Unknown", "score" => FillNull::MEAN))`. It applies when no option key (`newValue`, `targetColumns`, ...) is used, and each column can get its own value. Before, this shape threw "Please specify newValue".
2. Fixed PHP 8.2+ "Creation of dynamic property" deprecations in `Describe`, used by MEAN/MEDIAN fills.

# Version 1.6.1
1. Fix bug when targetColumns is null

# Version 1.6.0
1. Change README

# Version 1.5.0
    1. Add `strict` property to both `DropNull` and `FillNull`. Set to true will enable strict comparison.


# Version 1.0.0
    1. DropNull
    2. FillNull