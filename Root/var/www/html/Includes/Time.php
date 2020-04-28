
                                
                                        <div id="cDisplay" class="cDisplay" onload="displayClock()"></div>
                                        <div id="dDisplay" class="dDisplay" onload="displayDate()"></div>
                                        <script> 
                                            function displayClock(){
                                                var date = new Date();
                                                var h = date.getHours();
                                                var m = date.getMinutes();
                                                var s = date.getSeconds();
                                                var session = "AM"
    
                                                if(h == 0){
                                                    h = 12;
                                                }
                                                
                                                if(h > 12){
                                                    h = h - 12;
                                                    session = "PM";
                                                }

                                                m = (m < 10) ? "0" + m : m;
                                                s = (s < 10) ? "0" + s : s;
                                                
                                                var time = h + ":" + m + ":" + s + " " + session;
                                                document.getElementById("cDisplay").innerText = time;
                                                document.getElementById("cDisplay").textContent = time;
                                                
                                                setTimeout(displayClock, 1000);
                                                
                                            }

                                            function displayDate()
                                            {
                                                var d = new Date();
                                                var months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                                                var days = ["Sun", "Mon", "Tues", "Wed", "Thurs", "Fri", "Sat"];
                                                var theDate = days[d.getDay()] + " " + ordinal_suffix_of(d.getDate()) + " " + months[d.getMonth()] + " " + d.getFullYear()
                                                document.getElementById("dDisplay").innerHTML = theDate;
                                                                                                
                                                setTimeout(displayDate, 10000);
                                            }
                                            
                                            function ordinal_suffix_of(i) {
                                                var j = i % 10,
                                                    k = i % 100;
                                                if (j == 1 && k != 11) {
                                                    return i + "st";
                                                }
                                                if (j == 2 && k != 12) {
                                                    return i + "nd";
                                                }
                                                if (j == 3 && k != 13) {
                                                    return i + "rd";
                                                }
                                                return i + "th";
                                            }

                                            displayClock();
                                            displayDate();
                                        </script>