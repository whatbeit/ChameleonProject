using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Shared;

namespace CharacterServer.Console
{
    [ConsoleHandler("setloglevel", 2, "<LogType,Value>")]
    public class SetLogLevel : IConsoleHandler
    {
        public bool HandleCommand(string command, List<string> args)
        {
            int number;
            bool converted = Int32.TryParse(args[1], out number);

            if (converted && number == 1)
                args[1] = "true";
            else if (converted && number == 0)
                args[1] = "false";

            try
            {
                if (Log.SetLogLevel(args[0], Convert.ToBoolean(args[1])))
                {
                    Log.Success("Console", String.Format("{0} succesfully set to {1}", args[0], args[1]));
                    return true;
                }
                else
                    return false;
            }
            catch (IndexOutOfRangeException)
            {
                return false;
            }
            catch (FormatException)
            {
                return false;
            }
       }

    }
}