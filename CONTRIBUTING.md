# Introduction

First of all, thank you for wishing to contribute to the ToDo&Co Application.
This documentation will explain how you can help and inform you of the different rules.

#### Table Of Content

[Bug tracking](#bug-tracking)  
[New Enhancement](#new-enhancement)  
[Styleguides](#styleguides)  
[Pull request](#pull-request)

## How to contribute

### Bug tracking

Before submitting a new bug report, please check the issues of the repository to avoid duplicates except if the issue is closed, in that case link the closed issue to your new one.
Then, if the bug isn't already reported, you can add a **New Issue** according to the following format:

- **The title should be clear**. It should contains a short sentence describing the bug.  
- The issue should contain **as much informations as possible**, explaining how and where the bug appears, and as far as possible   explain why do you see this as a bug.  
- **Link** every **issue, pull request or discussion** which could be relatable to the bug.  
- Provide **screenshots**.  
- Apply the **"bug" label** to the issue.  

Those are the minimum informations required, add all of the datas you think are relevant.

### New Enhancement

Before submitting a new enhancement, please check the issues of the repository to avoid duplicates.  
Then, you can describe your proposal in a **New Issue** taking care to follow theses rules:

- Write a **clear title**, less informations as possible, just the necessary.  
- First, write a **short introduction** to explain how it could be **useful** for the application.  
- Then, **promote your suggestion**. It should contain **as many details as possible**, with clear **examples**, **screenshot** if needed or other application where this enhancement exists.  
- **Link** every **issue, pull request or discussion** which could be relatable to your enhancement.  
- Apply the **"enhancement" label** to the issue.  

### Styleguides

- #### **Git Commit**  

First of all, before writing a single line of code, create a new branch from the dev one to work on.  
Next, be sure to commit frequently, please avoid huge commit with all of your changes and prefer several small commits which contains related file -for example if you work on authentication and task management, create a first commit about authentication and a commit about task management-. Next, commits should be done following these rules.

- **Short and clear description**.  
- Use the "hopper" method, namely, write the **most important informations** such as the type of commit, the issued related, then add details if it's **necessary** such as which function is add.  
  For example, if you add the "Task creation", the commit could be _Task Management: add a task function_  
- If you are working on an enhancement precise the **action** (for example _ADD_, _UPDATE_, _DELETE_,...).  
- If you are working on documentation **precise shortly why** (for example _correcting mistakes_, _adding forgetted informations_, ...).  
- Comment every commits and **link them to the issue** they are related to.  

- #### **PHP**  

The committed PHP code should be **clear and legible**, it should follows the indentation, blank line could be use to avoid huge stack of lines, and as far as possible **document your code**, it isn't necessary to comment each line of code but a few lines before the added functions to briefly explain the following code would be appreciated.

- #### **Html/Twig**  

The committed html code should be **clear and legible**, it should follow the indentation. Don't hesitate to follow the Twig documentation to respect the **code style and standards**.  
The naming of the file should respect the standards of the application. For example a twig file which contains the "adding a task" form should be put in "template/task" and named "create.html.twig".

- ### Pull Request  

First, before adding a pull request, be sure your pull request validate at least one of those sentences.

- The changes submitted gives a solution to a **bug report**.  
- The changes submitted are related to an **issue**.  
- The changes submitted could improve the **code quality**.  

Then, you can add your pull request following these rules.

- Clear title, which should contains major informations about the role of the request.  
- Provide a clear description of the commits of your branch and all the modification you want to pull.  
- The pull request should always be put from your branch to the "_dev_" branch.  
