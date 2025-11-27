# Leantime Task Organiser
This is a little project to give a better overview of important tasks to complete.
It works by a widget on your Leantime Dashboard, where you can create different task lists that gets automatically ordered and prioritized based on your configurations.

There are several different "Sort Modules" that you can use to give a weight to a task. The higher the weight, the higher it will be in the dashboard task list.
You can use sort modules to order based on values such as Status, Project, Effort, etc.

If you have the Custom Fields plugin, you can also use sort modules for Bool, Checkbox and Radio custom fields.

<img width="300" alt="image" src="https://github.com/user-attachments/assets/92d22947-af87-44f7-b8dd-9720e1161dcc" />

<img width="300" alt="image" src="https://github.com/user-attachments/assets/e5cac1d5-de61-4b61-97d4-c576124c9040" />

## Configuration
You can configure your task lists under Account Settings.
There are a couple of fields that you can modify there:
* Title: The title of the task list
* Subtitle: A sentence shown below the title
* General
    - Max Tasks: The maximum tasks to show on each calculation
    - Persistency: For how long the tasks in the list should persist before considering new tasks in the list. The way it works, is that when you calculate a task list, only those tasks that is shown will be considered for reevaluation or reprioritization on later updates. (-1 for always recalculate the list)
    - Always Show: If the task list should always be expanded in the widget
    - Order: The order index of the list, a higher number means it will be earlier in the task lists
    - Use Tasks: If the list should consider normal Tasks
    - Use subtasks: If the list should consider sub-tasks
* Modules
    - A JSON file with the configurations for the modules

## Installation
To install this plugin, simply download the latest release ZIP file under releases and unzip it into the Leantime plugin folder.
