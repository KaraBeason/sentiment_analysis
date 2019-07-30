## Sentiment Analysis Block Plugin for Moodle

### Description
This block plugin provides course administrators the ability to analyze the sentiment of student submissions for "online text submission" 
type assignments in Moodle.  The sentiment analysis task uses Python's [Natural Language Toolkit (NLTK)](https://www.nltk.org/) along with 
the [TextBlob library](https://textblob.readthedocs.io/en/dev/) to analyze pieces of text for sentiment, producing a "polarity score" for 
the text which indicates whether the text expresses a negative, neutral, or positive sentiment.  
### Configuration
#### Python Path
The path to the python runtime executable is configured by an administrator upon installation of the plugin or via the "plugin overview" section of site administration in Moodle.
#### Assignment List
The list of assignments that the sentiment analysis task will process must be configured in the block instance settings, by turning  editing on and then selecting "configure this block" from a course where the block has been added.  
### Operation
Once an instance of the plugin has been added to a course, the user can then click the "Execute Task" button in the block instance.  The task will then be queued and run the next time cron is run for your installation of Moodle.  The frequency as which cron is run is dependent upon how your installation of Moodle has been configured by your administrator.  When cron is run, the task will be executed and each assignment in the assignment list (from the block instance configuration) will be analyzed for sentiment.  Meaning for each assignment, every text submission (final submission only) will be analyzed for sentiment and a report will be produced with the overall sentiment analysis followed by a list of each student's name and the sentiment analysis of their text submission for the assignment.  This report is saved in the instructor's private file area under a folder labeled "sentiment analysis" and the instructor will receive a notification letting them know their reports are available.  The report consists of an overall sentiment cover page, which shows the sentiment of all the assignment submissions for that assignment, followed by each individual assignment submission's sentiment polarity score.  Polarity scores are color coded: negative is red, neutral is grey, and positive is green.
