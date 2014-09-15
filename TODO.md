Cloudlab
========

- (IP) Refactor Code

- (DONE) URGENT: FIX SECURITY ISSUE (users can access pages even without logging in)
- (DONE) Back function
- Move actual files to S3

- (DONE) New File
- (DONE) Save File
- (DONE) Rename File
- (DONE) Delete File
- Submit Project
- Compile Project
- Run Project
- (DONE) Compile & Run
- (DONE) Preferences -> editor theme, font-size, line #, active line, print margin, wordwrap, softtabs, tabsize
- (DONE) Search & Replace
- (DONE) Create a status bar for feedback to the user
- (DONE) Open File (via DB)

- Add new and interesting commands and shortcuts

- hide folder path in compiler
- scanf(), fscanf/fprintf (might have to control access to these functions or completely ban them)

- (DONE) New Project
	- (DEFERRED) Restrict extensions
- (DONE) Edit Project (probably only rename)
- (DONE) Delete Project

Compiler
-------------------------
- Add support for arguments and flags


Complete Admin panel
---------------------------
- (DONE) Fill users table
- (DONE) Fill courses table
- Make buttons functional
	- Finish adding User and Course enrollment feature
	- (DONE) Might have to fix the getCourses SQL query
- (DONE) Make all forms and dialogs and make them functional
- (optional) Make an automatic script for creating user accounts


UI Enhancements
---------------------------
- (DONE) Change Terminal to Ace Editor
- (DONE) Beautify everything via Bootstrap (extremely important)
- (DONE) Change editor page layout
- (DONE) Fix all layouts
- (DONE) Make all menus functional
- (DONE) Make all dialogs working

(DONE) CloudLab v.2 (Think of a better name, please) (Maybe: Playground)
================
- (DONE) Think about how you can implement multiple users, multiple independent file copies, one file, one account.
- (DONE) Login page has a checkbox to choose either playground or full-fledged area
- (DONE) Multiple students can access the same file (but work on their own copies)


Q's E-mail
================
Hi Daniel,

As a followup on our discussion about cloudlab, here are some suggestions.
We'll need two environments:

1) Play area
- Here I and another instructor should be able to login and start creating 
C/C++ files. We should be able to organize files into Weeks (Week1, Week2, 
etc) or topics.

- multiple students should be able to login (perhaps using the same account) 
and view the files we have created in the previous step, edit the files (but 
not save them) and compile and run to see the results. Again, it is a play 
area where students view, edit, compile, and run C/C++ programs. Note that 
there may be multiple students viewing, editing, and compiling the same 
file.

2) Full fledged area
- Here the instructor should be able to create students accounts, and the 
students should be able to login, view some existing files, edit, and 
compile them, but alos they should be able to save changes they make to 
files, and create new files.

I look forward to seeing the fully functional system on Jan 3. The play area 
is priority.
Thanks and happy holidays.
q.

(DONE) Conference Paper
================================
- Related solutions:
	- Cloud9 and Compilr are the most serious contenders
	- Koding, codenode, codepad, PythonAnywhere, ideone (handles stdin input by asking users to type in the input in advance before compiling. It works, but we can do better.)
	- Even sourceLair handles stdin by sending the input in advance before compilong
	- But they do not have a means for use in academia
- (DONE) Talk about network issues during 2750 - we need more scalable solutions for concurrent users
- (DONE) We need a system that can handle large numbers of programmers concurrently with no sweats. We also need a scalable system as more users show interest in programming
- Can run multiple instances but currently only uses one (micro)
- Migrating completely to NodeJS (Express and Socket.IO) will reap huge benefits
- Currently, we only have a playground prototype running, although most of the user account system is functional
====================================

- provide a simple, unified, platform-independent programming environment and interface for students participating in Computer Science programming labs. The system will be implemented in the form of a web-based service powered by Amazon’s Elastic Compute Cloud (EC2) and S3 Storage cloud computing services, and will effectively operate as a virtual cloud-based Computer Science lab. This overall goal involves achieving the following sub-goals:
- Provide a cloud-based development environment that is easily accessible (can be accessed from any machine with a web browser)
- Provide a reliable, safe, and secure user experience
- Provide a scalable service, capable of handling large numbers of concurrent user requests
- Ease the burden of software installation and setup for both students and instructors
- Eliminate problems of cross-platform incompatibility
- Reduce dependency on the University’s computational resources
- Provide an easily accessible, private, secure, and scalable storage service for source files, along with the ability to edit, save, and delete such files
- Provide server-side compilation, program execution, and user feedback services


