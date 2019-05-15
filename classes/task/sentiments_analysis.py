# /**
#  * Sentiment Anaysis Task
#  *
#  * This python script takes the name of a directory as a command line argument
#       and analyzes each .txt file in the directory for sentiment.  A report is
#       produced in the same directory containing the overall sentiment of the 
#       collectio of text files, as well as an individual sentiment analysis
#       of each file.
#  * @author      Kara Beason <beasonke@appstate.edu>
#  * @copyright   (c) 2019 Appalachian State Universtiy, Boone, NC
#  * @license     GNU General Public License version 3
#  */
from textblob import TextBlob
import os
import sys
import codecs
from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import letter, A4
from reportlab.lib.colors import HexColor

# Check that a command line argument is present.
if len(sys.argv) != 2:
    print("Usage: python sentiments_analysis.py <directory>")
    exit(0)

directory = str(sys.argv[1])

# Check that the command line argurment is indeed a valid directory.
if (not os.path.isdir(directory)) or (not os.path.exists(directory)):
    print("Directory %s is not valid.", directory)
    exit(0)

# Check the current y position and whether the next line or decrease
#   of y position by value will run off the page.  Return a valid
#   new y value.
def is_new_page(report, y, value, height):
    if y - 100 < 0:
        report.showPage()
        y = height - 100
        return y
    else:
        y = y - value
        return y

# Determine whether the polarity score (integer passed in)
#   is negative (green), neutral (grey), or positive (green)
def get_polarity_color(polarity):
    if (polarity < -0.05):
        return '#FF0000'
    elif (polarity > 0.05):
        return '#008000'
    else:
        return '#808080'

# Create the body of the report named report_name.
#   Sentiments_list is the list of indivdual sentiment analyses
#   and overall is the overall sentiment of the list.
def print_report(report_name, sentiments_list, overall):
    # save the report in the directory passed in on command line.
    save_name = os.path.join(directory, report_name + ".pdf")
    # PDF lab canvas creation
    rep = canvas.Canvas(save_name, pagesize=letter)
    # although width is unused in this script it's apparently necessary for report lab.
    width, height = letter 
    # decrease y for the top of page margin.
    y = height - 100
    # Print the overall sentiment analysis on the first page.
    rep.drawString(100, y, "Overall Sentiment: ")
    for label, val in overall.__dict__.items():
        if label == "sentiment_assessments":
            y = is_new_page(rep, y, 15, height)
            for l, v in val.__dict__.items():
                y = is_new_page(rep, y, 15, height)
                rep.drawString(125, y, str(l))
                if isinstance(v, float):
                    y = is_new_page(rep, y, 15, height)
                    if l == "polarity":
                        color = get_polarity_color(v)
                        rep.setFillColor(HexColor(color))
                    rep.drawString(125, y, str(v))
                    rep.setFillColor(HexColor('#000000'))
    # new page.
    rep.showPage()
    # Sentiment Analysis by text file/ student
    for user, sentiment in sentiments_list.iteritems():
        y = height - 100
        rep.drawString(100, y, "Student Name: ")
        rep.drawString(225, y, user)
        for name, value in sentiment.__dict__.items():
            y = is_new_page(rep, y, 15, height)
            rep.drawString(125, y, str(name))
            y = is_new_page(rep, y, 15, height)

            if isinstance(value, float):
                if (name == "polarity"):
                    color = get_polarity_color(value)
                    rep.setFillColor(HexColor(color))
                rep.drawString(125, y, str(value))
                rep.setFillColor(HexColor('#000000'))
            else:
                for word in value:
                    y = is_new_page(rep, y, 15, height)
                    rep.drawString(125, y, str(word))
        rep.showPage()
    rep.save()

# Create the sentiments list.
sentiments = dict()
overall = ""
# Iterate over each text file in the directory
for filename in os.listdir(directory):
    if filename.endswith(".txt"):
        # Username will be the first part of the filename for the report.
        username = filename.split('_')[0]
        filename = os.path.join(directory, filename)
        line = ""
        with codecs.open(filename, "r",encoding='utf-8', errors='ignore') as fdata:
            # Read in the text file.
            line += fdata.read()
            # Add contents of text file to overall text.
            overall += line
            # Convert to textblob object
            line = TextBlob(line)
            # Add sentiment assessment to sentiments dict under name <username>
            sentiments[username] = line.sentiment_assessments
# Convert entire text into a textblob object
overall = TextBlob(overall)
# Get the sentiment assessment of the whole thing.
overall_sentiment = overall.sentiment_assessments
# Create and save the report.
print_report("output", sentiments, overall)