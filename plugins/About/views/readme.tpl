<div class="row">
<div class="span6">
    <article>
        <h2>
            {{script_name}}
        </h2>

        <p>
            Thank you for installing PHPDevShell, if you can read this, it probably means that the installation
            went well, the system also connected to the database without problems. Now you can start writing your
            application inside PHPDevShell as plugins or plain linked scripts.
            Remember to check back at
            <a href="http://www.phpdevshell.org" title="Visit us for support, updates and faq.">phpdevshell.org</a>
            every now and then to see if you are running the latest version or use the plugin manager to check.
            PHPDevShell is constantly being worked on improving security, stability and speed.
        </p>

        <p>
            PHPDevShell will always try to keep true to "keep it light, keep it simple, keep it stable".
            This is so that we can continue to fully support, maintain and improve the system
            without worrying about cluttering functionality.
        </p>

        <p>
            For all support related queries please use our wiki located at
            <a href="http://www.phpdevshell.org/content/official-documentation">docs.phpdevshell.org</a>.
            Here you will find documentation to help you get started as well, no documentation is provided with
            the distribution package. If you feel that you have a good idea to add or improve PHPDevShell,
            please share it with the community at
            <a href="http://www.phpdevshell.org/content/official-documentation">docs.phpdevshell.org</a>.
        </p>

        <p>
            PHPDevShell is free software; you can redistribute it and/or
            modify it under the terms of the GNU Lesser General Public
            License as published by the Free Software Foundation; either
            version 2.1 of the License, or (at your option) any later version.
        </p>

        <p>
            PHPDevShell is distributed in the hope that it will be useful,
            but WITHOUT ANY WARRANTY; without even the implied warranty of
            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
            Lesser General Public License for more details.
        </p>

        <p>
            You should have received a copy of the GNU Lesser General Public
            License along with this library; if not, write to the Free Software
            Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
        </p>

        <h3>Thanks</h3>

        <p>
            I would like to thank everyone involved in this project for their support and motivation.
            Special thanks goes to God and all the contributors of <a href="http://www.phpdevshell.org">PHPDevShell</a>.
        </p>

        <p>
            I would also like to thank all Open Source Projects involded used to make PHPDevShell possible.
        </p>

        <p>
            Special thanks go to our sponsoring hosting provider, who provides reliable VPS/Cloud solutions and
            is a vivid pro open-source company:
        </p>

        <p>
            <a href="http://www.host1plus.com" style="padding: 3px;" class="ui-corner-all">
                <img src="{{aurl}}/plugins/AdminTools/images/host1plus.jpg" alt="Host1Plus" title="Host1Plus">
            </a>
        </p>

        <p>
            A lot of work goes into a project like this, I ask nothing in return, however,
            sometimes I need a word of motivation.
            Please take a minute and say thanks, it would be nice to hear what PHPDevShell does for you.
        </p>

        <h3>You agree to this License when using PHPDevShell;</h3>

        <p>
            <strong>The GNU/LGPL 2.1 unlike GNU/GPL allows PHPDevShell to be used in proprietary software.</strong>
        </p>

        <p>
            Package : PHPDevShell is a RAD Framework aimed at developing administrative applications.<br/>
            Copyright &copy; 2007 Jason Schoeman<br/>
            Author: Jason Schoeman<br/>
            Contact: titan [at] phpdevshell [dot] org.<br/>
            Full license under <a href="readme/licensed_under_lgpl">/readme/licensed_under_lgpl</a><br/>
            or<br/>
            <a href="http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html">http://www.gnu.org/licenses/old-licenses/lgpl-2.1.html</a><br/>
        </p>

        <p>
            Good luck and enjoy!
        </p>
    </article>
</div>
<div class="span6">
    <article>
        <h2>Some Features...</h2>

        <h3>Themes</h3>

        <p>
            Using the most stable and best HTML5 and CSS3 features the theme is what wraps your application.
            Standard elements are used, in other words, there is no need to learn endless classes to make your
            application look consistent on every page. Just type HTML in your view as you know it.
        </p>

        <p>
            It’s extremely easy to create your own theme, and it is 100% customizable.
            PHPDevShell was built using minimal styling making it easier for you to build upon.
        </p>

        <h2>Bootstrap</h2>

        <p>
            PHPDevShell UI was built using Bootstrap. Bootstrap is a clean and powerful UI CSS framework
            for JQuery, it offers you an easy to override CCS framework for your project. If you like to fall in
            with Bootstrap and have a solid base right from the start you have even less work waiting for you.
        </p>

        <h3>Notifications Preview</h3>
        {{{note}}}
        {{{warning}}}
        {{{ok}}}
        {{{info}}}
        <h3>Some Form Elements</h3>

        <p>
            <label for="text">{{#i}}Text Field{{/i}}</label>
            <input id="text" type="text" size="20" name="text" value="" title="{{#i}}Sample Text Title.{{/i}}">
        </p>

        <p>
            <label for="textreq">{{#i}}Text Field Required{{/i}}</label>
            <input id="textreq" type="text" size="20" name="textreq" value="" required="required"
                   title="{{#i}}Sample Required Text Title{{/i}}">
        </p>

        <p>
            <button type="submit" name="sample" value="sample" class="btn">
                {{#i}}Sample Button{{/i}}
            </button>
            <button type="submit" name="sample_" value="sample_" class="btn btn-primary">
                {{#i}}Another Sample Button{{/i}}
            </button>
        </p>
        <h3>Other Elements Styling</h3>

        <h1>Heading 1</h1>

        <h2>Heading 2</h2>

        <h3>Heading 3</h3>
        <h4>Heading 4</h4>
        <h5>Heading 5</h5>
        <h6>Heading 6</h6>

        <p>
            This is <abbr title="title">abbreviation</abbr><br>
            This is <strong>strong</strong><br>

            This is <em>emphasis</em><br>
            This is <b>bold text</b><br>
            This is <i>italic text</i><br>
            This is <cite>cite</cite><br>
            This is
            <small>small text</small>
            <br>

            This is
            <del>deleted text</del>
            <br>
            This is
            <ins>inserted text</ins>
            <br>
            This is <dfn>defining instance</dfn><br>
            This is <kbd>user input</kbd><br>

            This is <samp>sample output</samp><br>
            This is <q>inline quotation</q> <br>
            These are <sub>subscript</sub> and <sup>superscript</sup><br>
            This is <var>a variable</var>
        </p>
        <h3>Tables</h3>
        <table class="table">
            <caption><em>A test table with a thead, tfoot, and tbody elements</em></caption>
            <thead>
            <tr>
                <th>Table Header One</th>
                <th>Table Header Two</th>
                <th>Table Header Image</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>TD One</td>
                <td>TD Two</td>
                <td>{{{urlbutton}}}</td>
            </tr>
            <tr>
                <td>TD One</td>
                <td>TD Two</td>
                <td>{{{urlbutton}}}</td>
            </tr>
            <tr>
                <td>TD One</td>
                <td>TD Two</td>
                <td>{{{urlbutton}}}</td>
            </tr>
            <tr>
                <td>TD One</td>
                <td>TD Two</td>
                <td>{{{urlbutton}}}</td>
            </tr>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">tfoot footer - some text for a footer or pagination</td>
            </tr>
            </tfoot>
        </table>
        <h3>User Interface Images</h3>

        <p>
            Pick from over three thousand images for your next applications ui functionality,
            this done with a simple function.
        </p>

        <p>
            {{{img1}}} {{{img2}}} {{{img3}}} {{{img4}}}
        </p>
    </article>
</div>
</div>
