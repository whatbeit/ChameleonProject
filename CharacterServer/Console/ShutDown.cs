using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using Shared;

namespace CharacterServer.Console
{
    [ConsoleHandler("shutdown", 0, "<time in second>")]
    public class ShutDown : IConsoleHandler
    {
        public bool HandleCommand(string command, List<string> args)
        {
            try
            {
                int waittime;

                if (Int32.TryParse(args[0], out waittime))
                    Program.WaitAndExit(waittime * 1000);
                else
                    Program.WaitAndExit(Program.Config.ShutDownTimer);

            }
            catch (ArgumentOutOfRangeException)
            {
                Program.WaitAndExit(Program.Config.ShutDownTimer);
            }
            return true;
        }
    }
}
