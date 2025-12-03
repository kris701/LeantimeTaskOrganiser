# Leantime Task Organiser
This is a little project to give a better overview of important tasks to complete.
It works by a widget on your Leantime Dashboard, where you can create different task lists that gets automatically ordered and prioritized based on your configurations.

There are several different "Sort Modules" that you can use to give a weight to a task. The higher the weight, the higher it will be in the dashboard task list.
You can use sort modules to order based on values such as Status, Project, Effort, etc.

This plugin also support some of the other Leantime plugins:
* **Custom Fields**: If you have this plugin, you can use sort modules for Bool, Checkbox and Radio custom fields.
* **Strategies**: If you have this plugin, you can use sort modules for things like strategy names.

If you have the Custom Fields plugin, you can also use sort modules for Bool, Checkbox and Radio custom fields.

<img width="400" alt="image" src="https://github.com/user-attachments/assets/a3020fce-2199-4fe6-b6b5-20a583795385" />

<img width="400" alt="image" src="https://github.com/user-attachments/assets/2e407718-4d8f-4bb1-8921-9dc62f0c9cef" />

> [!WARNING]
> If you disable, uninstall or update the plugin all caches and user settings will be reset!

## Configuration
You can configure your task lists under Account Settings.
There are a couple of fields that you can modify there:
* **Title**: The title of the task list
* **Subtitle**: A sentence shown below the title
* **General**
    - **Max Tasks**: The maximum tasks to show on each calculation
    - **Persistency**: For how long the tasks in the list should persist before considering new tasks in the list. The way it works, is that when you calculate a task list, only those tasks that is shown will be considered for reevaluation or reprioritization on later updates. (-1 for always recalculate the list)
    - **Always Show**: If the task list should always be expanded in the widget
    - **Hide list when empty**: If the task list should be hidden if there are no tasks in it.
    - **Order**: The order index of the list, a higher number means it will be earlier in the task lists
    - **Show Weights**: If you should be able to see the weight of each task in the widget.
* **Item Selection**
    - **Use Tasks**: If the list should consider normal Tasks
    - **Use subtasks**: If the list should consider sub-tasks
    - **Use bugs**: If the list should consider bugs
* **Modules**
    - A JSON file with the configurations for the modules

## Installation
To install this plugin, simply download the latest release ZIP file under releases and unzip it into the Leantime plugin folder.

## Development
For local development (on Windows) you can run the `dockerInit.ps1` script to download the Leantime Docker git and run the compose file.

You can then subsequently execute `dockerDeploy.ps1` to copy the current `./TaskOrganiser` folder into the containers and restart them at the same time.

