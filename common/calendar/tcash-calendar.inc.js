/*
===============================================================
tcash-calendar.inc.js
===============================================================
Reusable calendar component - supporting Javascript functions
---------------------------------------------------------------
*/

//CLASS DEFINITION
function Calendar() {
    this.monthlist = ["January", "February", "March", "April", "May", "June", "July",
                      "August", "September", "October", "November", "December"];
  
    //populate the calendar with current month -1
    this.populatePrevious = function () {
        if (this.month -1 >= 0) {
            this.month -= 1;
        } else {
            this.month = 11;
            this.year -= 1;
        }
        this.populateCalendar(this.year, this.month);
    };

    //populate the calendar with current month +1
    this.populateNext = function () {
        if (this.month +1 <= 11) {
            this.month += 1;
        } else {
            this.month = 0;
            this.year += 1;
        }
        this.populateCalendar(this.year, this.month);
    };
    
    //function to populate the calendar. 
    //Parameters supply the required year & month (base-0)
    this.populateCalendar = function (year, month) {

        this.month = month;
        this.year = year;
        this.today = new Date();
        this.today.setHours(0, 0, 0, 0);
        this.displayMonth = this.monthlist[this.month] + " " + this.year;
        this.cellDateXref = [];
        
        //calculate the day of the week for the 1st day of the month and store in this.firstDOM
        var date = new Date();
        date.setFullYear(this.year, this.month, 1);
        this.firstDOM = date.getDay();

        //calculate how many total days there are in the month & store to this.lastDOM
        if (["3", "5", "8", "10"].indexOf(this.month.toString()) !== -1) {
            this.lastDOM = 30;
        } else if (["1"].indexOf(this.month.toString()) !== -1) {
            if (new Date(this.year, 1, 29).getMonth() === 1) {
                this.lastDOM = 29;
            } else {
                this.lastDOM = 28;
            }
        } else {
            this.lastDOM = 31;
        }
        
        //set the display month header
        document.getElementById("thMonthName").innerHTML = this.displayMonth;

        var r, c;
        var cellVal = "";
        var currDate = 0;

        //loop around every row (r) and every column (c) in the calendar
        for (r=1; r<=6; r++) {
            for (c=0; c<=6; c++) {

                //Determine which date number to put in the current cell
                //Use blanks if before first day of month, or after last day of month
                if ((r===1 && c < this.firstDOM) || currDate >= this.lastDOM) {
                    cellVal = "";
                } else {
                    currDate += 1;
                    cellVal = currDate;
                }

                //Calculate the cell ID from the day number & set innerHTML 
                var currCell = document.getElementById("c" + r + "-" + c);
                currCell.innerHTML = cellVal;

                
                //Set CLICK event of this cell. This executes in its own context, 
                //so cannot reference any variables outside of its own scope
                currCell.addEventListener("click", function () {

                    //Only do something on click if there is a number in the box
                    if (this.innerHTML !== "") {
                        
                        //get the cell ID of the last selected cell
                        //(it's stored in a hidden field in the HTML)
                        //and reset it's styles to the default classes
                        var oLastCell = document.getElementById(
                            document.getElementById("txtLastDate").value
                        );

                        if (oLastCell != null) {
                            if (oLastCell.className !== "tCell tdCalToday") {
                                oLastCell.className = "tCell tdCalDay";
                            }
                        }

                        //there is a hidden field in the HTML that stores the 
                        //selected date in YYYY-MM-DD format. Update it to show
                        //which date has just been clicked
                        var oChosenCell = document.getElementById("txtCalDate");
                        oChosenCell.value = objCalendar.year + "-" + 
                                            ("00" + (objCalendar.month+1)).slice(-2) + "-" +
                                            ("00" + this.innerHTML).slice(-2);

                        //update the "last selected cell" hidden field & fire the 
                        //click event of the "selected date" field. This is to 
                        //allow the main form to hook an event handler onto the click
                        //and update its own date field with the selected date.
                        document.getElementById("txtLastDate").value = this.id;
                        document.getElementById("txtCalDate").click();

                        if (this.className !== "tCell tdCalToday") {
                            this.className = "tCell tdCalSelected";
                        }
                    }
                });

                //cellDateXref holds a cross-reference table of all cell names
                //and all dates. Populate it with details of this cell.
                this.cellDateXref.push({id: "c" + r + "-" + c, date: currDate});
                

                //Set style if this cell contains today's date
                if (cellVal !== "") {
                
                    //get full date based on current cell value
                    var dCell = new Date();
                    dCell.setFullYear(this.year, this.month, currDate);
                    dCell.setHours(0, 0, 0, 0);

                    //the "+" operator turns the dates into a numeric, 
                    //which makes them easier to compare.
                    if (+dCell == +this.today) {
                        currCell.className = "tCell tdCalToday"; 
                    } else {
                        currCell.className = "tCell tdCalDay"; 
                    }
                } else {
                    currCell.className = "tCell tdCalDay"; 
                }
            }
        }
    };

    
    //function to find required date string value in the 
    //Xref array and fire the CLICK event of that cell
    this.highlightDate = function(dDateString) {
        
        //1. get year & month from date parameter
        var lYear = parseInt(dDateString.substring(0, 4));
        var lMonth = parseInt(dDateString.substring(5, 7))-1;
        var lDay = parseInt(dDateString.substring(8,10));
        var lTargetCell = null;
        
        //2. repopulate calendar from year & month
        this.populateCalendar(lYear, lMonth);
        
        //3. find date value in the cross-reference table
        //   and store the target cell ID
        for (var iCell = 0; iCell < this.cellDateXref.length; iCell++) {
            if (this.cellDateXref[iCell].date == lDay) {
                lTargetCell = this.cellDateXref[iCell].id;
                break;
            }
        }

        //4. fire the click event of the cell, using the found ID.
        if (lTargetCell != null) {
            document.getElementById(lTargetCell).click();
        }
    }

}


//ATTACH EVENTS to "previous" and "next" month buttons
document.getElementById("thMonthPrev").addEventListener("click", function() {
    objCalendar.populatePrevious();
});

document.getElementById("thMonthNext").addEventListener("click", function() {
    objCalendar.populateNext();
});

