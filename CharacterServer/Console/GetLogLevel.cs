using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Shared;

namespace CharacterServer.Console
{
    [ConsoleHandler("getloglevel", 1, "<Type>")]
    public class GetLogLevel : IConsoleHandler
    {
        public bool HandleCommand(string command, List<string> args)
        {
            switch (args[0].ToLower())
            {
                case "tcp":
                case "debug":
                case "dump":
                case "notice":
                case "error":
                case "info":
                case "successs":
                    break;
                default:
                    return false;
            }

            if (Log.GetLogLevel(args[0]))
                Log.Info("Console", String.Format("{0} is now: ON", args[0]));
            else
                Log.Info("Console", String.Format("{0} is now: OFF", args[0]));
            return true;
        }
    }
}
