using System;
using System.Collections.Generic;
using System.Text;
using Shared;

namespace CharacterServer
{
    [ConsoleHandler("help", 1, "<command>")]
    public class ConsoleHelp : IConsoleHandler
    {
        public bool HandleCommand(string command, List<string> args)
        {


            switch (args[0])
            {
                case "create":
                    Log.Success("Command Create", "Usage: .create : <username,Password> Example: .create qwerty 12345");
                    break;

                case "realm":
                    Log.Success("Command Realm", "Usage: .realm : <id(<21),type(16-48-80),lang(0-1-3-5)> Example: .realm 33 48 3");
                    break;

                case "help":
                    Log.Success("Command Help", "Usage: .help : <command> Example: .help realm");
                    break;

                default:
                    Log.Notice("Command Unknown", "You did something wrong");
                    break;

            }

            return true;
        }

    }
}