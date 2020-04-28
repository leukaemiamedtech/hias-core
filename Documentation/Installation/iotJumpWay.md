# Peter Moss Leukemia AI Research
## HIAS - Hospital Intelligent Automation System

# iotJumpWay Installation Guide
[![GeniSysAI Server](../../Media/Images/HIAS-Hospital-Intelligent-Automation-System.png)](https://github.com/LeukemiaAiResearch/HIAS-Hospital-Intelligent-Automation-System)

Now you need to install the iotJumpWay and setup some appications and devices. The following part of the tutorial will guide you through this.

&nbsp;

# Useful Guides
- [Find out about the iotJumpWay](https://www.iotjumpway.com/how-it-works "Find out about the iotJumpWay") 
- [Find out about the iotJumpWay Dev Program](https://www.iotjumpway.com/developers/ "Find out about the iotJumpWay Dev Program") 
- [Get started with the iotJumpWay Dev Program](https://www.iotjumpway.com/developers/getting-started "Get started with the iotJumpWay Dev Program") 

&nbsp;

# iotJumpWay Beta Account 
First of all you should [register your free iotJumpWay account](https://www.iotjumpway.com/console/register "register your free iotJumpWay account"), all services provided by the iotJumpWay are also entirely free within fair limits.

&nbsp;

# iotJumpWay Location
[![iotJumpWay](https://www.iotjumpway.com/console/media/images/console-home.jpg)](https://www.iotJumpWay.com/console)
Once you have signed in to the iotJumpWay Developer Console, you need to create your iotJumpWay location [(Documentation)](https://www.iotjumpway.com/developers/getting-started-locations "(Documentation)"). 

Your Location represents the building you are physically in, ie university, hospital, center etc.

&nbsp;

# iotJumpWay Zone
[![iotJumpWay](https://www.iotjumpway.com/console/media/images/console-location-zones.jpg)](https://www.iotJumpWay.com/console)

Next you need to create your iotJumpWay zone [(Documentation)](https://www.iotjumpway.com/developers/getting-started-zones "(Documentation)"). 

Your Zones represent rooms or spaces within your iotJumpWay Location, ie lab, reception, office etc.

&nbsp;

# iotJumpWay Application
[![iotJumpWay](https://www.iotjumpway.com/console/media/images/console-location-devices-applications.jpg)](https://www.iotJumpWay.com/console)

Next you need to create your iotJumpWay application [(Documentation)](https://www.iotjumpway.com/developers/getting-started-applications "(Documentation)"). 

Your application will be used by the server to provide communication between the UI and the iotJumpWay using websockets. Applications have the ability to send commands, warnings and statuses from and to all devices and applications connected to the location.

&nbsp;

# Install iotJumpway Python MQTT Library
Now you should install the iotJumpWay MQTT library.
 
```
pip install JumpWayMQTT
```

&nbsp;

# Continue With Install
Now you have completed the iotJumpWay setup, you can continue with the [Installation Guide](Installation.md "Installation Guide").

&nbsp;

# Contributing

The Peter Moss Acute Myeloid & Lymphoblastic Leukemia AI Research project encourages and welcomes code contributions, bug fixes and enhancements from the Github.

Please read the [CONTRIBUTING](../../CONTRIBUTING.md "CONTRIBUTING") document for a full guide to forking our repositories and submitting your pull requests. You will also find information about our code of conduct on this page.

## Contributors

- **AUTHOR:** [Adam Milton-Barker](https://www.leukemiaresearchassociation.ai.com/team/adam-milton-barker "Adam Milton-Barker") - [Peter Moss Leukemia AI Research](https://www.leukemiaresearchassociation.ai "Peter Moss Leukemia AI Research") Founder & Intel Software Innovator, Sabadell, Spain

&nbsp;

# Versioning

We use SemVer for versioning. For the versions available, see [Releases](../../releases "Releases").

&nbsp;

# License

This project is licensed under the **MIT License** - see the [LICENSE](../../LICENSE "LICENSE") file for details.

&nbsp;

# Bugs/Issues

We use the [repo issues](../../issues "repo issues") to track bugs and general requests related to using this project. See [CONTRIBUTING](../../CONTRIBUTING.md "CONTRIBUTING") for more info on how to submit bugs, feature requests and proposals.