# Important Include Files

This directory contains critical files for the Task Manager application. These files should be protected and maintained carefully.

## Essential Files

### 1. config.php
- Application configuration
- Database settings
- Security parameters
- Session settings
- Time zone configuration
- Debug settings

### 2. password_policy.php
- Password validation rules
- Password hashing functionality
- Password verification
- Security requirements:
  - Minimum length: 8 characters
  - Requires uppercase letters
  - Requires lowercase letters
  - Requires numbers
  - Requires special characters

### 3. session.php
- Session security management
- CSRF protection
- Session timeout handling
- Session regeneration
- Security features:
  - HttpOnly cookies
  - Secure cookie setting
  - Session validation
  - Anti-hijacking protection

### 4. logger.php
- Error logging system
- Security monitoring
- User activity tracking
- Log rotation (30 days)
- Log levels:
  - INFO: General information
  - WARNING: Potential issues
  - ERROR: Critical problems

## Security Notes

1. Never modify these files directly on the production server
2. Always backup these files before making changes
3. Keep the includes directory protected via .htaccess
4. Regularly check logs for suspicious activity
5. Update security settings as needed

## Maintenance

1. Check logs regularly
2. Update password policies as needed
3. Monitor session security
4. Keep configuration updated
5. Backup regularly

## Warning

⚠️ These files contain sensitive security settings. Always:
- Keep backups
- Use version control
- Test changes in development
- Monitor for unauthorized changes
