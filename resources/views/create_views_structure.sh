#!/bin/bash

# Create directories
mkdir -p auth courses assessments reviews teachers

# Create files under auth
touch auth/login.blade.php
touch auth/register.blade.php

# Create the home page
touch home.blade.php

# Create files under courses
touch courses/show.blade.php

# Create files under assessments
touch assessments/create.blade.php
touch assessments/edit.blade.php
touch assessments/show.blade.php

# Create files under reviews
touch reviews/create.blade.php
touch reviews/show.blade.php
touch reviews/top.blade.php

# Create files under teachers
touch teachers/upload.blade.php

echo "Folders and files created successfully!"

